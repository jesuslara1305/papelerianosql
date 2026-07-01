@echo off
title Restaurar Base de Datos - MongoDB
echo ===================================================
echo Restaurando todas las colecciones de 'productos'...
echo ===================================================
echo.

set CURRENT_DIR=%~dp0

:: Restaura los datos locales. Usa --drop para limpiar datos viejos si lo corren mas de una vez.
mongorestore --db=productos "C:\xampp\htdocs\SanrioShop\database\db_backup\productos" --drop

echo.
echo [OK] Base de datos restaurada por completo. ¡Listo para usar!
echo.
pause1