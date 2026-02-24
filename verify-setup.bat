@echo off
REM Script de verificación pre-deployment para Windows
REM Ejecuta este script antes de tu primer push

echo =========================================
echo 🔍 Verificación Pre-Deployment
echo =========================================
echo.

set ERRORS=0
set WARNINGS=0
set SUCCESS=0

echo 📋 Verificando archivos necesarios...
echo.

REM Verificar .gitignore
if exist ".gitignore" (
    echo [92m✓[0m .gitignore existe
    set /a SUCCESS+=1
) else (
    echo [91m✗[0m .gitignore no encontrado
    set /a ERRORS+=1
)

REM Verificar workflow
if exist ".github\workflows\deploy.yml" (
    echo [92m✓[0m GitHub Actions workflow configurado
    set /a SUCCESS+=1
) else (
    echo [91m✗[0m GitHub Actions workflow no encontrado
    set /a ERRORS+=1
)

REM Verificar config.production
if exist "config\config.production.php.example" (
    echo [92m✓[0m Plantilla de configuración existe
    set /a SUCCESS+=1
) else (
    echo [91m✗[0m config.production.php.example no encontrado
    set /a ERRORS+=1
)

REM Verificar post-deploy script
if exist "deploy-scripts\post-deploy.sh" (
    echo [92m✓[0m Script post-deployment existe
    set /a SUCCESS+=1
) else (
    echo [91m✗[0m Script post-deployment no encontrado
    set /a ERRORS+=1
)

echo.
echo 🔐 Verificando seguridad...
echo.

REM Verificar .gitignore contiene config.php
findstr /C:"config/config.php" .gitignore >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [92m✓[0m config.php está en .gitignore
    set /a SUCCESS+=1
) else (
    echo [91m✗[0m config.php NO está en .gitignore ^(¡RIESGO DE SEGURIDAD!^)
    set /a ERRORS+=1
)

REM Verificar .gitignore contiene logs
findstr /C:"*.log" .gitignore >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo [92m✓[0m Archivos .log están en .gitignore
    set /a SUCCESS+=1
) else (
    echo [91m✗[0m Archivos .log NO están en .gitignore
    set /a ERRORS+=1
)

echo.
echo 📁 Verificando estructura de directorios...
echo.

if exist "logs\" (
    echo [92m✓[0m Directorio logs\ existe
    set /a SUCCESS+=1
) else (
    echo [91m✗[0m Directorio logs\ no existe
    set /a ERRORS+=1
)

if exist "config\" (
    echo [92m✓[0m Directorio config\ existe
    set /a SUCCESS+=1
) else (
    echo [91m✗[0m Directorio config\ no existe
    set /a ERRORS+=1
)

if exist "controllers\" (
    echo [92m✓[0m Directorio controllers\ existe
    set /a SUCCESS+=1
) else (
    echo [91m✗[0m Directorio controllers\ no existe
    set /a ERRORS+=1
)

if exist "models\" (
    echo [92m✓[0m Directorio models\ existe
    set /a SUCCESS+=1
) else (
    echo [91m✗[0m Directorio models\ no existe
    set /a ERRORS+=1
)

if exist "views\" (
    echo [92m✓[0m Directorio views\ existe
    set /a SUCCESS+=1
) else (
    echo [91m✗[0m Directorio views\ no existe
    set /a ERRORS+=1
)

echo.
echo 🔧 Verificando Git...
echo.

if exist ".git\" (
    echo [92m✓[0m Repositorio Git inicializado
    set /a SUCCESS+=1
    
    REM Verificar remote
    git remote -v | findstr "github.com" >nul 2>&1
    if %ERRORLEVEL% EQU 0 (
        echo [92m✓[0m Remote de GitHub configurado
        set /a SUCCESS+=1
    ) else (
        echo [91m✗[0m Remote de GitHub NO configurado
        set /a ERRORS+=1
        echo ℹ️  Ejecuta: git remote add origin https://github.com/TU_USUARIO/TU_REPO.git
    )
    
    REM Verificar rama actual
    for /f "tokens=*" %%i in ('git branch --show-current') do set BRANCH=%%i
    if "%BRANCH%"=="main" (
        echo [92m✓[0m En rama principal: %BRANCH%
        set /a SUCCESS+=1
    ) else if "%BRANCH%"=="master" (
        echo [92m✓[0m En rama principal: %BRANCH%
        set /a SUCCESS+=1
    ) else (
        echo [93m⚠[0m  No estás en rama main/master ^(actual: %BRANCH%^)
        set /a WARNINGS+=1
    )
) else (
    echo [91m✗[0m NO es un repositorio Git
    set /a ERRORS+=1
    echo ℹ️  Ejecuta: git init
)

echo.
echo 📝 Verificando archivos SQL...
echo.

set SQL_COUNT=0
for %%f in (*.sql) do set /a SQL_COUNT+=1

if %SQL_COUNT% GTR 0 (
    echo [92m✓[0m Encontrados %SQL_COUNT% archivos SQL
    set /a SUCCESS+=1
    for %%f in (*.sql) do echo   - %%f
) else (
    echo [93m⚠[0m  No se encontraron archivos SQL
    set /a WARNINGS+=1
)

echo.
echo =========================================
echo 📊 Resumen de Verificación
echo =========================================
echo [92mExitosos:[0m %SUCCESS%
echo [93mAdvertencias:[0m %WARNINGS%
echo [91mErrores:[0m %ERRORS%
echo.

if %ERRORS% EQU 0 (
    echo [92m✅ Todo listo para deployment![0m
    echo.
    echo Próximos pasos:
    echo 1. Configura los GitHub Secrets:
    echo    - Settings → Secrets → Actions → New repository secret
    echo    - SSH_PRIVATE_KEY, SSH_HOST, SSH_PORT, SSH_USER
    echo.
    echo 2. Configura Hostinger ^(ver .github\DEPLOYMENT.md^)
    echo.
    echo 3. Haz tu primer deployment:
    echo    git add .
    echo    git commit -m "Initial deployment setup"
    echo    git push origin main
    echo.
    exit /b 0
) else (
    echo [91m❌ Hay errores que deben corregirse antes del deployment[0m
    echo.
    echo Revisa los errores marcados arriba y corrígelos.
    echo Consulta .github\DEPLOYMENT.md para más detalles.
    echo.
    exit /b 1
)
