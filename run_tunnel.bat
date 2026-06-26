@echo off
title UdenShop Serveo Tunnel
echo Starting Serveo Tunnel...
powershell -ExecutionPolicy Bypass -File "%~dp0tunnel.ps1"
pause
