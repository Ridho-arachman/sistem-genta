<?php
$content = file_get_contents("resources/views/welcome.blade.php");

$old = "
                <div class=\"programs-container\">
                    <div class=\"program-card\" style=\"--program-color: #E50000;\">
                        <div class=\"program-header\">
                            <span class=\"program-badge\">GENTA MBG</span>
                            <h3 class=\"program-title\">Program Makan Bergizi</h3>
                        </div>
                        <p class=\"program-desc\">
                            Program pangan berbasis kedaulatan pangan lokal dengan standar gizi 
                            untuk masyarakat Banten.
                        </p>
                    </div>
                    <div class=\"program-card\" style=\"--program-color: #2E7D32;\">
                        <div class=\"program-header\">
                            <span class=\"program-badge\">GENTA Pertanian</span>
                            <h3 class=\"program-title\">Pertanian Berkelanjutan</h3>
                        </div>
                        <p class=\"program-desc\">
                            Pelatihan pertanian organik, teknologi pertanian tepat guna, 
                            dan manajemen lahan untuk petani Banten.
                        </p>
                    </div>
                </div>";

$new = "
                <div class=\"programs-container\">
                    <div class=\"program-card\" style=\"--program-color: #E50000;\">
                        <div class=\"program-image\" style=\"background-image: url('https://images.unsplash.com/photo-1607582278043-57198ac8da43?q=80&w=800&auto=format&fit=crop');\">
                            <div class=\"program-overlay\">
                                <x-heroicon name=\"bowl-food\" class=\"w-10 h-10 text-white\" />
                            </div>
                        </div>
                        <div class=\"program-content\">
                            <div class=\"program-header\">
                                <span class=\"program-badge\">GENTA MBG</span>
                                <h3 class=\"program-title\">Program Makan Bergizi</h3>
                            </div>
                            <p class=\"program-desc\">
                                Program pangan berbasis kedaulatan pangan lokal dengan standar gizi 
                                untuk masyarakat Banten.
                            </p>
                            <ul class=\"program-highlights\">
                                <li><strong>Target:</strong> 500.000+ siswa SD/MI di Banten</li>
                                <li><strong>Cakupan:</strong> 5.000+ sekolah di 6 kabupaten/kota</li>
                                <li><strong>Menu:</strong> Beras merah, ikan, telur, sayur, buah lokal</li>
                                <li><strong>Manfaat:</strong> Meningkatkan konsentrasi & prestasi belajar</li>
                            </ul>
                        </div>
                    </div>
                    <div class=\"program-card\" style=\"--program-color: #2E7D32;\">
                        <div class=\"program-image\" style=\"background-image: url('https://images.unsplash.com/photo-1625246333195-78d9c38ad449?q=80&w=800&auto=format&fit=crop');\">
                            <div class=\"program-overlay\">
                                <x-heroicon name=\"sprout\" class=\"w-10 h-10 text-white\" />
                            </div>
                        </div>
                        <div class=\"program-content\">
                            <div class=\"program-header\">
                                <span class=\"program-badge\">GENTA Pertanian</span>
                                <h3 class=\"program-title\">Pertanian Berkelanjutan</h3>
                            </div>
                            <p class=\"program-desc\">
                                Pelatihan pertanian organik, teknologi pertanian tepat guna, 
                                dan manajemen lahan untuk petani Banten.
                            </p>
                            <ul class=\"program-highlights\">
                                <li><strong>Target:</strong> 10.000+ petani kecil di Banten</li>
                                <li><strong>Fokus:</strong> Organik, irigasi hemat air, varietas unggul</li>
                                <li><strong>Teknologi:</strong> IoT sensor tanah, drone monitoring, app mobile</li>
                                <li><strong>Hasil:</strong> Produksi naik 30%, biaya turun 20%</li>
                            </ul>
                        </div>
                    </div>
                </div>";

$content = file_get_contents("resources/views/welcome.blade.php");
$content = str_replace($old, $new, $content);
file_put_contents("resources/views/welcome.blade.php", $content);
echo "Program section updated\n";
?>
