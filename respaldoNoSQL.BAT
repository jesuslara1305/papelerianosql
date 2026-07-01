@echo off
title Respaldo Total de Base de Datos - MongoDB
echo ===================================================
echo Respaldando la base de datos 'productos' completa...
echo ===================================================
echo.

:: Obtiene la ruta de la carpeta donde reside este archivo .bat
set CURRENT_DIR=%~dp0

:: Ejecuta el respaldo dentro de la carpeta del proyecto
mongodump --db=productos --out="C:\xampp\htdocs\SanrioShop\database\db_backup"

echo.
echo [OK] Carpeta 'db_backup' generada exitosamente.
echo.
pause
