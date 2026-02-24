#!/bin/bash

###############################################################################
# Script de verificación pre-deployment
# Ejecuta este script antes de tu primer push para verificar la configuración
###############################################################################

echo "========================================="
echo "🔍 Verificación Pre-Deployment"
echo "========================================="
echo ""

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Contadores
ERRORS=0
WARNINGS=0
SUCCESS=0

# Función para verificar
check() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✓${NC} $2"
        ((SUCCESS++))
    else
        echo -e "${RED}✗${NC} $2"
        ((ERRORS++))
    fi
}

warn() {
    echo -e "${YELLOW}⚠${NC}  $1"
    ((WARNINGS++))
}

info() {
    echo -e "ℹ️  $1"
}

echo "📋 Verificando archivos necesarios..."
echo ""

# Verificar .gitignore
if [ -f ".gitignore" ]; then
    check 0 ".gitignore existe"
else
    check 1 ".gitignore no encontrado"
fi

# Verificar workflow de GitHub Actions
if [ -f ".github/workflows/deploy.yml" ]; then
    check 0 "GitHub Actions workflow configurado"
else
    check 1 "GitHub Actions workflow no encontrado"
fi

# Verificar config.production.php.example
if [ -f "config/config.production.php.example" ]; then
    check 0 "Plantilla de configuración existe"
else
    check 1 "config.production.php.example no encontrado"
fi

# Verificar post-deploy script
if [ -f "deploy-scripts/post-deploy.sh" ]; then
    check 0 "Script post-deployment existe"
else
    check 1 "Script post-deployment no encontrado"
fi

echo ""
echo "🔐 Verificando seguridad..."
echo ""

# Verificar que config.php está excluido
if grep -q "config/config.php" .gitignore; then
    check 0 "config.php está en .gitignore"
else
    check 1 "config.php NO está en .gitignore (¡RIESGO DE SEGURIDAD!)"
fi

# Verificar que logs están excluidos
if grep -q "*.log" .gitignore; then
    check 0 "Archivos .log están en .gitignore"
else
    check 1 "Archivos .log NO están en .gitignore"
fi

# Verificar que .env está excluido
if grep -q ".env" .gitignore; then
    check 0 ".env está en .gitignore"
else
    warn ".env no está en .gitignore (agregar si lo usas)"
fi

echo ""
echo "📁 Verificando estructura de directorios..."
echo ""

# Verificar directorios críticos
[ -d "logs" ] && check 0 "Directorio logs/ existe" || check 1 "Directorio logs/ no existe"
[ -d "config" ] && check 0 "Directorio config/ existe" || check 1 "Directorio config/ no existe"
[ -d "controllers" ] && check 0 "Directorio controllers/ existe" || check 1 "Directorio controllers/ no existe"
[ -d "models" ] && check 0 "Directorio models/ existe" || check 1 "Directorio models/ no existe"
[ -d "views" ] && check 0 "Directorio views/ existe" || check 1 "Directorio views/ no existe"

echo ""
echo "🔧 Verificando Git..."
echo ""

# Verificar si es un repositorio git
if [ -d ".git" ]; then
    check 0 "Repositorio Git inicializado"
    
    # Verificar remote
    if git remote -v | grep -q "github.com"; then
        check 0 "Remote de GitHub configurado"
    else
        check 1 "Remote de GitHub NO configurado"
        info "Ejecuta: git remote add origin https://github.com/TU_USUARIO/TU_REPO.git"
    fi
    
    # Verificar rama
    BRANCH=$(git branch --show-current)
    if [ "$BRANCH" == "main" ] || [ "$BRANCH" == "master" ]; then
        check 0 "En rama principal: $BRANCH"
    else
        warn "No estás en rama main/master (actual: $BRANCH)"
    fi
else
    check 1 "NO es un repositorio Git"
    info "Ejecuta: git init"
fi

echo ""
echo "📝 Verificando archivos SQL..."
echo ""

# Contar archivos de migración
SQL_FILES=$(ls -1 *.sql 2>/dev/null | wc -l)
if [ $SQL_FILES -gt 0 ]; then
    check 0 "Encontrados $SQL_FILES archivos SQL"
    ls -1 *.sql | while read file; do
        info "  - $file"
    done
else
    warn "No se encontraron archivos SQL (esquema.sql, migrations, etc.)"
fi

echo ""
echo "🌐 Verificación de conectividad (opcional)..."
echo ""

# Probar conexión SSH (opcional, puede tardar)
read -p "¿Probar conexión SSH a Hostinger? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    if timeout 5 ssh -p 65002 -o ConnectTimeout=5 -o StrictHostKeyChecking=no u329333801@147.79.84.57 "echo 'SSH OK'" 2>/dev/null; then
        check 0 "Conexión SSH a Hostinger exitosa"
    else
        check 1 "No se pudo conectar por SSH"
        info "Verifica que la clave SSH esté configurada en Hostinger"
    fi
fi

echo ""
echo "========================================="
echo "📊 Resumen de Verificación"
echo "========================================="
echo -e "${GREEN}Exitosos:${NC} $SUCCESS"
echo -e "${YELLOW}Advertencias:${NC} $WARNINGS"
echo -e "${RED}Errores:${NC} $ERRORS"
echo ""

if [ $ERRORS -eq 0 ]; then
    echo -e "${GREEN}✅ Todo listo para deployment!${NC}"
    echo ""
    echo "Próximos pasos:"
    echo "1. Asegúrate de configurar los GitHub Secrets:"
    echo "   - Settings → Secrets → Actions → New repository secret"
    echo "   - SSH_PRIVATE_KEY, SSH_HOST, SSH_PORT, SSH_USER"
    echo ""
    echo "2. Configura Hostinger (ver .github/DEPLOYMENT.md)"
    echo ""
    echo "3. Haz tu primer deployment:"
    echo "   git add ."
    echo "   git commit -m 'Initial deployment setup'"
    echo "   git push origin main"
    echo ""
    exit 0
else
    echo -e "${RED}❌ Hay errores que deben corregirse antes del deployment${NC}"
    echo ""
    echo "Revisa los errores marcados arriba y corrígelos."
    echo "Consulta .github/DEPLOYMENT.md para más detalles."
    echo ""
    exit 1
fi
