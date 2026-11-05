@echo off
setlocal
cd /d "%~dp0"
title Web Psikolog - Laravel Server

echo ========================================================
echo   Memulai Server Web Psikolog (Laravel)
echo   Alamat  : http://127.0.0.1:8000
echo   Browser : Google Chrome
echo   Tekan Ctrl + C di jendela ini untuk mematikan server.
echo ========================================================
echo.

rem Menjalankan Chrome setelah jeda 2 detik (agar server siap menerima koneksi)
start "" /b powershell -WindowStyle Hidden -Command "Start-Sleep -Seconds 2; try { Start-Process 'chrome.exe' 'http://127.0.0.1:8000' } catch { Start-Process 'http://127.0.0.1:8000' }"

rem Menjalankan server Laravel di foreground
php artisan serve
