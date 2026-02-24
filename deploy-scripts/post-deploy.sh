#!/bin/bash

###############################################################################
# Script de post-deployment para CRM Bahari Aqua
# Se ejecuta en el servidor después de rsync
###############################################################################

set -e  # Salir si algún comando falla

DEPLOY_PATH="/home/u329333801/domains/crm.bahariaqua.com/public_html"
LOG_FILE="$DEPLOY_PATH/logs/deployment.log"

echo "========================================" | tee -a "$LOG_FILE"
echo "Post-deployment script started at $(date)" | tee -a "$LOG_FILE"
echo "========================================" | tee -a "$LOG_FILE"

# Navegar al directorio del proyecto
cd "$DEPLOY_PATH" || exit 1

# 1. Verificar estructura de directorios
echo "[1/6] Verificando estructura de directorios..." | tee -a "$LOG_FILE"
mkdir -p logs uploads
chmod 755 logs uploads

# Crear archivos .gitkeep si no existen
touch logs/.gitkeep uploads/.gitkeep

# 2. Verificar permisos de archivos críticos
echo "[2/6] Configurando permisos..." | tee -a "$LOG_FILE"
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod 755 logs uploads

# 3. Limpiar archivos temporales antiguos
echo "[3/6] Limpiando archivos temporales..." | tee -a "$LOG_FILE"
find logs/ -name "*.log" -type f -mtime +30 -delete 2>/dev/null || true

# 4. Verificar archivo de configuración
echo "[4/6] Verificando configuración..." | tee -a "$LOG_FILE"
if [ ! -f "config/config.php" ]; then
    echo "⚠️  WARNING: config/config.php no encontrado!" | tee -a "$LOG_FILE"
    echo "   Por favor, crea config/config.php basándote en config.production.php.example" | tee -a "$LOG_FILE"
else
    echo "✓ Archivo de configuración encontrado" | tee -a "$LOG_FILE"
fi

# 5. Ejecutar migraciones SQL pendientes (si existen)
echo "[5/6] Verificando migraciones SQL..." | tee -a "$LOG_FILE"
MIGRATION_FLAG="$DEPLOY_PATH/.last_migration"
LATEST_MIGRATION=$(ls -t migration_*.sql 2>/dev/null | head -n 1 || echo "")

if [ -n "$LATEST_MIGRATION" ]; then
    if [ ! -f "$MIGRATION_FLAG" ] || [ "$LATEST_MIGRATION" != "$(cat $MIGRATION_FLAG 2>/dev/null)" ]; then
        echo "⚠️  Nueva migración detectada: $LATEST_MIGRATION" | tee -a "$LOG_FILE"
        echo "   Por favor, ejecuta manualmente la migración desde phpMyAdmin o consola MySQL" | tee -a "$LOG_FILE"
        echo "$LATEST_MIGRATION" > "$MIGRATION_FLAG"
    else
        echo "✓ No hay nuevas migraciones" | tee -a "$LOG_FILE"
    fi
else
    echo "✓ No hay archivos de migración" | tee -a "$LOG_FILE"
fi

# 6. Verificar estado del sistema
echo "[6/6] Verificando estado del sistema..." | tee -a "$LOG_FILE"
if [ -f "index.php" ]; then
    echo "✓ index.php encontrado" | tee -a "$LOG_FILE"
else
    echo "❌ ERROR: index.php no encontrado!" | tee -a "$LOG_FILE"
    exit 1
fi

# Registro de finalización
echo "========================================" | tee -a "$LOG_FILE"
echo "✅ Post-deployment completado exitosamente at $(date)" | tee -a "$LOG_FILE"
echo "========================================" | tee -a "$LOG_FILE"

exit 0
