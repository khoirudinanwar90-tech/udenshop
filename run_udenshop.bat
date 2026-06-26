@echo off
title UdenShop Dev Launcher
echo ===================================================
echo               UDENSHOP DEV LAUNCHER
echo ===================================================
echo.

:: 1. Start MySQL Server
echo [1/3] Starting MySQL Database...
start "MySQL Database" cmd /c "c:\xampp\mysql\bin\mysqld.exe --standalone"
timeout /t 3 /nobreak >nul

:: 2. Start Laravel Artisan Serve
echo [2/3] Starting Laravel Development Server...
start "Laravel Dev Server" cmd /c "c:\xampp\php\php.exe artisan serve --port=8000"
timeout /t 2 /nobreak >nul

:: 3. Start SSH Tunnel (Serveo)
echo [3/3] Starting SSH Tunnel...
start "SSH Tunnel (Serveo)" cmd /c "powershell -ExecutionPolicy Bypass -File "%~dp0tunnel.ps1""

echo.
echo ===================================================
echo All services have been started!
echo - Database: Port 3306
echo - Laravel Server: http://127.0.0.1:8000
echo - Active URL: Check storage\logs\current_url.txt
echo ===================================================
echo.
pause
