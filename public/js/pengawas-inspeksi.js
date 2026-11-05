document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('laporanForm');
    const submitBtn = document.getElementById('submitBtn');
    
    let photo = null; // {dataUrl, lat, lng, accuracy, address, gpsStatus}
    
    // Show/hide inline error message below an input
    function showError(id, message) {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = message;
            el.classList.remove('hidden');
        }
    }

    function hideError(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.add('hidden');
        }
    }

    // Convert lat/lng to OSM tile x,y at given zoom
    function latLngToTile(lat, lng, zoom) {
        const n = Math.pow(2, zoom);
        const xFloat = (lng + 180) / 360 * n;
        const yFloat = (1 - Math.log(Math.tan(lat * Math.PI / 180) + 1 / Math.cos(lat * Math.PI / 180)) / Math.PI) / 2 * n;
        const x = Math.floor(xFloat);
        const y = Math.floor(yFloat);
        return { x, y, z: zoom, fracX: xFloat - x, fracY: yFloat - y };
    }

    // Compress image to maxSizeMB using canvas downscale + JPEG quality
    async function compressImage(file, maxSizeMB = 5) {
        if (file.size <= maxSizeMB * 1024 * 1024) {
            return file;
        }
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const maxDim = 1600;
                    let { width, height } = img;
                    if (width > height && width > maxDim) {
                        height = (maxDim / width) * height;
                        width = maxDim;
                    } else if (height > maxDim) {
                        width = (maxDim / height) * width;
                        height = maxDim;
                    }
                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    let quality = 0.8;
                    let dataUrl = canvas.toDataURL('image/jpeg', quality);
                    while (dataUrl.length * 0.75 > maxSizeMB * 1024 * 1024 && quality > 0.1) {
                        quality -= 0.1;
                        dataUrl = canvas.toDataURL('image/jpeg', quality);
                    }
                    resolve(dataUrl);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    // Update hidden inputs
    function updateHiddenInputs() {
        document.getElementById('photo_data').value = photo?.dataUrl || '';
        document.getElementById('latitudeInput').value = photo?.lat || '';
        document.getElementById('longitudeInput').value = photo?.lng || '';
        document.getElementById('addressInput').value = photo?.address || '';
    }

    // Show/hide loading overlay
    function showLoading(show) {
        const overlay = document.getElementById('loadingOverlay');
        if (show) {
            overlay.classList.remove('hidden');
        } else {
            overlay.classList.add('hidden');
        }
    }

    // Show/hide submit button spinner
    function showSpinner() {
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');
        const btn = document.getElementById('submitBtn');
        if (btnText) btnText.classList.add('hidden');
        if (btnSpinner) btnSpinner.classList.remove('hidden');
        if (btn) btn.disabled = true;
    }

    function hideSpinner() {
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');
        const btn = document.getElementById('submitBtn');
        if (btnText) btnText.classList.remove('hidden');
        if (btnSpinner) btnSpinner.classList.add('hidden');
        if (btn) btn.disabled = false;
    }

    // Render photo preview
    function renderPreview() {
        const el = document.getElementById('photo_preview');
        if (!photo || !photo.dataUrl) {
            el.innerHTML = '';
            el.classList.add('hidden');
            return;
        }

        let gpsStatusHtml;
        if (photo.gpsStatus === 'loading') {
            gpsStatusHtml = `<div class="mt-1 pt-1 border-t border-[var(--color-border)]">
                <span class="text-[9px] font-medium text-[var(--color-secondary)]">⏳ GPS memproses…</span>
                <p class="text-[9px] text-[var(--color-text-muted)]">Tunggu hingga selesai sebelum kirim laporan</p>
               </div>`;
        } else if (photo.lat && photo.lng) {
            gpsStatusHtml = `<div class="mt-1 pt-1 border-t border-[var(--color-border)]">
                <span class="text-[9px] font-medium text-[var(--color-primary)]">✅ GPS tersimpan</span>
                <p class="text-[9px] text-[var(--color-text-muted)]">${photo.lat}, ${photo.lng}</p>
                <p class="text-[9px] text-[var(--color-text-muted)]">${photo.address ? photo.address : 'GPS tersimpan'}</p>
               </div>`;
        } else {
            gpsStatusHtml = `<div class="mt-1 pt-1 border-t border-[var(--color-border)]">
                <span class="text-[9px] font-medium text-red-600">❌ GPS gagal</span>
                <p class="text-[9px] text-[var(--color-text-muted)]">Lokasi tidak tersedia. Coba ambil foto ulang.</p>
               </div>`;
        }

        el.innerHTML = `
            <div class="relative group rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-2">
                <img src="${photo.dataUrl}" alt="Foto dapur" class="w-full h-48 object-cover rounded-md cursor-pointer" data-action="open-photo-preview">
                <button type="button" data-action="remove-photo" class="delete-photo-btn absolute top-2 right-4 z-50 bg-white bg-opacity-100 p-2 rounded-full text-red-600 hover:text-red-800 shadow-lg" aria-label="Hapus foto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
                ${gpsStatusHtml}
            </div>
        `;
        el.classList.remove('hidden');
    }

    // Open photo preview modal
    function openPhotoPreview() {
        if (!photo || !photo.dataUrl) return;

        const previewImg = document.getElementById('previewImage');
        const previewGps = document.getElementById('previewGps');
        const modal = document.getElementById('photoPreviewModal');

        previewImg.src = photo.dataUrl;

        if (photo.lat && photo.lng) {
            previewGps.innerHTML = '📍 ' + photo.lat + ', ' + photo.lng + (photo.accuracy ? '  (±' + Math.round(photo.accuracy) + 'm)' : '') +
                (photo.address ? '<br><span class="text-[0.7rem]">' + photo.address + '</span>' : '');
        } else {
            previewGps.textContent = 'GPS tidak tersedia';
        }

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Close photo preview modal
    function closePhotoPreview() {
        document.getElementById('photoPreviewModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Toggle camera or open preview based on whether photo exists
    function toggleCamera() {
        if (photo && photo.dataUrl) {
            openPhotoPreview();
        } else {
            document.getElementById('photo_input').click();
        }
    }

    // Remove photo
    function removePhoto() {
        photo = null;
        renderPreview();
        updateHiddenInputs();
    }

    // Handle photo selection from native camera/file input
    async function handlePhotoSelected(input) {
        const files = Array.from(input.files);
        if (!files.length) return;

        const file = files[0];
        if (!file.type.startsWith('image/')) {
            showError('error-foto', 'Bukan file gambar');
            input.value = '';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            showError('error-foto', 'Foto terlalu besar (maks 5MB)');
            input.value = '';
            return;
        }

        // Show loading
        showLoading(true);

        try {
            // Compress if > 5MB
            let dataUrl = file.size > 5 * 1024 * 1024 ? await compressImage(file, 5) : await new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.readAsDataURL(file);
            });

            const photoId = 'photo_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            photo = {
                id: photoId,
                dataUrl: dataUrl,
                lat: '',
                lng: '',
                accuracy: '',
                address: '',
                gpsStatus: 'loading'
            };

            renderPreview();
            updateHiddenInputs();

            // Reset input
            input.value = '';

            // Process GPS + watermark in background
            getCurrentLocationForPhoto(photoId, dataUrl);
        } catch (err) {
            console.error('Photo processing error:', err);
            showError('error-foto', 'Gagal memproses foto');
        } finally {
            showLoading(false);
        }
    }

    // Get GPS for a specific photo using Capacitor Geolocation, then create combined image
    async function getCurrentLocationForPhoto(photoId, originalDataUrl) {
        if (!photo || photo.id !== photoId) return;

        try {
            let position;

            if (window.Capacitor && window.Capacitor.Plugins && window.Capacitor.Plugins.Geolocation) {
                position = await window.Capacitor.Plugins.Geolocation.getCurrentPosition({
                    enableHighAccuracy: false,
                    timeout: 15000
                });
            } else if (navigator.geolocation) {
                position = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: false,
                        timeout: 15000,
                        maximumAge: 60000
                    });
                });
            } else {
                throw new Error('Geolocation not supported');
            }

            const accuracy = position.coords.accuracy;
            const lat = position.coords.latitude.toFixed(6);
            const lng = position.coords.longitude.toFixed(6);

            // Get full address via reverse geocoding (Nominatim / OpenStreetMap) — blocking
            let address = '';
            try {
                const resp = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&accept-language=id`);
                const data = await resp.json();
                address = data.display_name || '';
            } catch (err) {
                console.warn('Reverse geocode error:', err);
            }

            // Create combined image WITH address — photo + map + GPS + alamat
            const combinedDataUrl = await createCombinedImage(originalDataUrl, lat, lng, address);

            // Update photo with complete data
            photo.dataUrl = combinedDataUrl;
            photo.lat = lat;
            photo.lng = lng;
            photo.accuracy = accuracy;
            photo.address = address;
            photo.gpsStatus = 'success';

            renderPreview();
            updateHiddenInputs();
        } catch (err) {
            photo.lat = '';
            photo.lng = '';
            photo.accuracy = '';
            photo.address = '';
            photo.gpsStatus = 'error';
            
            let msg = 'GPS tidak tersedia';
            if (err.code === 1 || err.message?.includes('denied')) {
                msg += ' (izin lokasi ditolak)';
            } else if (err.code === 2 || err.message?.includes('unavailable')) {
                msg += ' (lokasi tidak tersedia)';
            } else if (err.code === 3 || err.message?.includes('timeout')) {
                msg += ' (timeout)';
            }
            console.warn('GPS error:', err);
            
            // Even without GPS, create combined image (just photo, no map overlay)
            const combinedDataUrl = await createCombinedImage(originalDataUrl, '', '', '');
            photo.dataUrl = combinedDataUrl;
            
            renderPreview();
            updateHiddenInputs();
        }
    }

    // Create combined image: photo + full-width map tile (bottom) + GPS text on map
    function createCombinedImage(photoDataUrl, lat, lng, address) {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                canvas.width = img.width;
                canvas.height = img.height;
                const ctx = canvas.getContext('2d');

                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                if (lat && lng) {
                    const tile = latLngToTile(parseFloat(lat), parseFloat(lng), 16);
                    const mapUrl = `https://tile.openstreetmap.org/${tile.z}/${tile.x}/${tile.y}.png`;

                    const mapImg = new Image();
                    mapImg.crossOrigin = 'anonymous';
                    mapImg.src = mapUrl;

                    mapImg.onload = () => {
                        drawMapAndGPS(ctx, canvas, mapImg, lat, lng, address, tile.fracX, tile.fracY);
                        resolve(canvas.toDataURL('image/jpeg', 0.85));
                    };
                    mapImg.onerror = () => {
                        drawGPSOnly(ctx, canvas, lat, lng, address);
                        resolve(canvas.toDataURL('image/jpeg', 0.85));
                    };
                } else {
                    resolve(canvas.toDataURL('image/jpeg', 0.85));
                }
            };
            img.src = photoDataUrl;
        });
    }

    function drawMapAndGPS(ctx, canvas, mapImg, lat, lng, address, fracX = 0.5, fracY = 0.5) {
        const marginLeft = 3;
        const marginRight = 30;
        const gap = 60;
        const mapH = Math.min(900, canvas.height * 0.66);
        const availableW = canvas.width - marginLeft - marginRight - gap;
        const mapW = Math.min(1200, availableW * 0.4);
        const textW = availableW - mapW;
        
        const mapX = marginLeft;
        const mapY = canvas.height - mapH - marginLeft;
        const textX = mapX + mapW + gap;

        ctx.drawImage(mapImg, mapX, mapY, mapW, mapH);

        const pinX = mapX + fracX * mapW;
        const pinY = mapY + fracY * mapH;
        const pinSize = 40;
        const pinTipY = pinSize * 1.2;
        
        ctx.save();
        ctx.translate(pinX, pinY - pinTipY);
        
        ctx.fillStyle = '#E50000';
        ctx.beginPath();
        ctx.arc(0, -pinSize * 0.3, pinSize * 0.55, Math.PI, 0);
        ctx.bezierCurveTo(
            pinSize * 0.55, -pinSize * 0.3,
            pinSize * 0.55, pinSize * 0.3,
            0, pinSize * 1.2
        );
        ctx.bezierCurveTo(
            -pinSize * 0.55, pinSize * 0.3,
            -pinSize * 0.55, -pinSize * 0.3,
            -pinSize * 0.55, -pinSize * 0.3
        );
        ctx.closePath();
        ctx.fill();
        
        ctx.fillStyle = '#AA0000';
        ctx.beginPath();
        ctx.arc(0, -pinSize * 0.3, pinSize * 0.45, Math.PI, 0);
        ctx.bezierCurveTo(
            pinSize * 0.45, -pinSize * 0.3,
            pinSize * 0.45, pinSize * 0.15,
            0, pinSize * 0.9
        );
        ctx.bezierCurveTo(
            -pinSize * 0.45, pinSize * 0.15,
            -pinSize * 0.45, -pinSize * 0.3,
            -pinSize * 0.45, -pinSize * 0.3
        );
        ctx.fill();
        
        ctx.fillStyle = '#FFFFFF';
        ctx.beginPath();
        ctx.arc(0, -pinSize * 0.3, pinSize * 0.22, 0, Math.PI * 2);
        ctx.fill();
        
        ctx.strokeStyle = '#FFFFFF';
        ctx.lineWidth = 4;
        ctx.beginPath();
        ctx.arc(0, -pinSize * 0.3, pinSize * 0.55, Math.PI, 0);
        ctx.bezierCurveTo(
            pinSize * 0.55, -pinSize * 0.3,
            pinSize * 0.55, pinSize * 0.3,
            0, pinSize * 1.2
        );
        ctx.bezierCurveTo(
            -pinSize * 0.55, pinSize * 0.3,
            -pinSize * 0.55, -pinSize * 0.3,
            -pinSize * 0.55, -pinSize * 0.3
        );
        ctx.stroke();
        
        ctx.restore();

        const textY = mapY;
        const textH = mapH;
        
        ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
        ctx.fillRect(textX - 18, textY, textW + 30, textH);
        
        ctx.fillStyle = '#FFFFFF';
        ctx.font = 'bold 84px system-ui, sans-serif';
        ctx.fillText(lat + ', ' + lng, textX, textY + 120);
        
        ctx.fillStyle = '#CCCCCC';
        ctx.font = '64px system-ui, sans-serif';
        ctx.fillText('GPS: ' + lat + ', ' + lng, textX, textY + 220);
        
        if (address) {
            const maxWidth = textW + 30;
            const words = address.split(' ');
            let lines = [];
            let currentLine = 'Alamat: ';
            
            for (const word of words) {
                const testLine = currentLine + word + ' ';
                const metrics = ctx.measureText(testLine);
                if (metrics.width > maxWidth && currentLine !== 'Alamat: ') {
                    lines.push(currentLine.trim());
                    currentLine = word + ' ';
                } else {
                    currentLine = testLine;
                }
            }
            if (currentLine.trim()) lines.push(currentLine.trim());
            
            ctx.fillStyle = '#FFFFFF';
            ctx.font = '68px system-ui, sans-serif';
            lines.forEach((line, i) => {
                ctx.fillText(line, textX, textY + 320 + i * 75);
            });
        }
    }

    function drawGPSOnly(ctx, canvas, lat, lng, address) {
        const marginLeft = 3;
        const marginRight = 30;
        const gap = 60;
        const mapH = Math.min(900, canvas.height * 0.66);
        const availableW = canvas.width - marginLeft - marginRight - gap;
        const mapW = Math.min(1200, availableW * 0.4);
        const textW = availableW - mapW;
        
        const mapX = marginLeft;
        const mapY = canvas.height - mapH - marginLeft;
        const textX = mapX + mapW + gap;
        
        const textY = mapY;
        const textH = mapH;
        
        ctx.fillStyle = 'rgba(0, 0, 0, 0.7)';
        ctx.fillRect(textX - 18, textY, textW + 30, textH);
        
        ctx.fillStyle = '#FFFFFF';
        ctx.font = 'bold 84px system-ui, sans-serif';
        ctx.fillText(lat + ', ' + lng, textX, textY + 120);
        
        ctx.fillStyle = '#CCCCCC';
        ctx.font = '64px system-ui, sans-serif';
        ctx.fillText('Peta tidak tersedia', textX, textY + 220);
        
        if (address) {
            const maxWidth = textW + 30;
            const words = address.split(' ');
            let lines = [];
            let currentLine = 'Alamat: ';
            
            for (const word of words) {
                const testLine = currentLine + word + ' ';
                const metrics = ctx.measureText(testLine);
                if (metrics.width > maxWidth && currentLine !== 'Alamat: ') {
                    lines.push(currentLine.trim());
                    currentLine = word + ' ';
                } else {
                    currentLine = testLine;
                }
            }
            if (currentLine.trim()) lines.push(currentLine.trim());
            
            ctx.fillStyle = '#FFFFFF';
            ctx.font = '68px system-ui, sans-serif';
            lines.forEach((line, i) => {
                ctx.fillText(line, textX, textY + 320 + i * 75);
            });
        }
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePhotoPreview();
        }
    });

    // Close modal on backdrop click
    document.getElementById('photoPreviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePhotoPreview();
        }
    });

    // Make submit handler async for native confirm
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        showSpinner();

        const dapurSelect = document.getElementById('dapur_id');
        if (!dapurSelect.value) {
            showError('error-dapur', 'Pilih dapur dulu');
            dapurSelect.focus();
            hideSpinner();
            return false;
        }
        hideError('error-dapur');

        const hasilSelects = document.querySelectorAll('select[name="hasil[]"]');
        const indikatorNames = ['Kebersihan dan sanitasi', 'Penyimpanan bahan', 'Proses pengolahan', 'Distribusi makanan', 'Peralatan dan fasilitas'];
        let hasilError = false;
        for (let i = 0; i < 5; i++) {
            if (!hasilSelects[i].value) {
                showError('error-hasil-' + (i + 1), 'Pilih hasil: ' + indikatorNames[i]);
                hasilSelects[i].focus();
                hasilError = true;
            } else {
                hideError('error-hasil-' + (i + 1));
            }
        }
        if (hasilError) {
            hideSpinner();
            return false;
        }

        if (!photo || !photo.dataUrl) {
            showError('error-foto', 'Foto dapur belum diisi. Klik area foto untuk mengambil gambar.');
            document.getElementById('card_photo').focus();
            hideSpinner();
            return false;
        }
        hideError('error-foto');

        if (photo.gpsStatus === 'loading') {
            showError('error-gps', 'Tunggu foto selesai diproses (GPS masih loading).');
            hideSpinner();
            return false;
        }

        if (!photo.lat || !photo.lng) {
            showError('error-gps', 'GPS gagal didapatkan. Lokasi wajib terisi sebelum kirim laporan. Coba ambil foto ulang.');
            hideSpinner();
            return false;
        }
        hideError('error-gps');

        updateHiddenInputs();

        const confirmed = window.confirm('Kirim laporan sekarang?\n\nData yang akan dikirim:\n- Dapur: ' + dapurSelect.options[dapurSelect.selectedIndex].text + '\n- Foto: tersedia\n- GPS: ' + photo.lat + ', ' + photo.lng);
        if (!confirmed) {
            hideSpinner();
            return false;
        }

        submitBtn.disabled = true;
        form.submit();
    });

    // Toggle camera / open photo preview
    document.getElementById('card_photo').addEventListener('click', function() {
        toggleCamera();
        hideError('error-foto');
    });

    // Handle photo selection
    document.getElementById('photo_input').addEventListener('change', function(e) {
        handlePhotoSelected(e.target);
        hideError('error-foto');
    });

    // Clear hasil error when user selects a value
    document.querySelectorAll('select[name="hasil[]"]').forEach(function(select, index) {
        select.addEventListener('change', function() {
            hideError('error-hasil-' + (index + 1));
        });
    });

    // Remove photo
    document.getElementById('photo_preview').addEventListener('click', function(e) {
        if (e.target.dataset.action === 'remove-photo') {
            removePhoto();
        }
    });

    // Open photo preview modal
    document.getElementById('photo_preview').addEventListener('click', function(e) {
        if (e.target.dataset.action === 'open-photo-preview') {
            openPhotoPreview();
        }
    });

    // Close modal on backdrop click
    document.getElementById('photoPreviewModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePhotoPreview();
        }
    });

    // Close photo preview button
    document.getElementById('photoPreviewModal').querySelector('[data-action="close-photo-preview"]').addEventListener('click', closePhotoPreview);
});
