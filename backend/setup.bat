@echo off
echo ========================================================
echo   SETUP PROJECT: SISTEM PENGARSIPAN NASKAH CAKRAWALA
echo ========================================================
echo.

if not exist .env (
    echo [1/5] Menyalin file konfigurasi .env...
    copy .env.example .env
) else (
    echo [1/5] File .env sudah ada.
)

echo [2/5] Menginstal dependensi Composer (PHP)...
call composer install

echo [3/5] Membuat Application Key...
call php artisan key:generate

echo [4/5] Menginstal dependensi NPM & Build aset...
call npm install
call npm run build

echo [5/5] Menghubungkan storage naskah...
call php artisan storage:link

echo.
echo ========================================================
echo   SETUP SELESAI DENGAN SUKSES!
echo --------------------------------------------------------
echo   Pastikan MySQL di Laragon/XAMPP sudah AKTIF.
echo   Lalu jalankan migrasi & data contoh dengan:
echo     php artisan migrate --seed
echo.
echo   Setelah itu jalankan server:
echo     php artisan serve
echo   Buka browser di: http://127.0.0.1:8000
echo ========================================================
pause
