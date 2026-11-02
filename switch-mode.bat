@echo off
echo ==========================================
echo  GENTA - Mode Switcher
echo ==========================================
echo.

if "%1"=="" (
    echo Usage: switch-mode [dev|mobile]
    echo.
    echo   dev     - Development mode (localhost only)
    echo   mobile  - Mobile/LAN mode (HP + laptop via LAN IP)
    echo.
    exit /b 1
)

if "%1"=="dev" (
    echo [MODE: DEVELOPMENT - Localhost only]
    echo.
    echo 1. Menghapus public/hot (jika ada)...
    if exist public\hot del public\hot
    echo.
    echo 2. Menjalankan Laravel + Vite dev server...
    echo    Akses: http://localhost:8000 (hanya laptop)
    echo.
    start "Laravel Server" cmd /k "php artisan serve --host=127.0.0.1 --port=8000"
    timeout /t 2 /nobreak >nul
    start "Vite Dev Server" cmd /k "npm run dev"
    echo.
    echo Server berjalan. Tekan Ctrl+C di window server untuk berhenti.
    pause
    exit /b 0
)

if "%1"=="mobile" (
    echo [MODE: MOBILE/LAN - HP + Laptop]
    echo.
    echo 1. Menghentikan server lama...
    taskkill /F /IM php.exe 2>nul
    taskkill /F /IM node.exe 2>nul
    echo.
    echo 2. Menghapus public/hot...
    if exist public\hot del public\hot
    echo.
    echo 3. Build aset statis (npm run build)...
    npm run build
    echo.
    echo 4. Clear cache Laravel...
    php artisan view:clear
    php artisan config:clear
    php artisan route:clear
    php artisan cache:clear
    echo.
    echo 5. Menjalankan server LAN (0.0.0.0:8000)...
    echo    Akses HP:  http://192.168.101.6:8000
    echo    Akses Laptop: http://localhost:8000
    echo.
    start "Laravel LAN Server" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"
    echo.
    echo Server berjalan. Tekan Ctrl+C di window server untuk berhenti.
    pause
    exit /b 0
)

echo Error: Mode tidak dikenal. Gunakan 'dev' atau 'mobile'.
exit /b 1