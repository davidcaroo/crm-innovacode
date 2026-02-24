# Comandos Útiles - CRM Bahari Aqua

## Git y Control de Versiones

### Inicializar repositorio (si aún no está hecho)
```bash
git init
git add .
git commit -m "Initial commit - CRM Bahari Aqua"
git branch -M main
git remote add origin https://github.com/TU_USUARIO/TU_REPOSITORIO.git
git push -u origin main
```

### Workflow diario
```bash
# Ver estado
git status

# Agregar cambios
git add .

# Commit
git commit -m "Descripción de cambios"

# Push (esto dispara el deployment automático)
git push origin main
```

### Ver logs de deployment
```bash
# En GitHub
# Ve a: Actions → Deploy to Hostinger → [último workflow]

# Desde SSH
ssh -p 65002 u329333801@147.79.84.57
tail -f /home/u329333801/domains/crm.bahariaqua.com/public_html/logs/deployment.log
```

## Conexión SSH a Hostinger

### Conectarse
```bash
ssh -p 65002 u329333801@147.79.84.57
```

### Navegar al proyecto
```bash
cd /home/u329333801/domains/crm.bahariaqua.com/public_html/
```

## Base de Datos

### Conectar a MySQL
```bash
mysql -u u329333801_crmuser -p u329333801_crm_bahari
```

### Backup de base de datos
```bash
# Crear backup
mysqldump -u u329333801_crmuser -p u329333801_crm_bahari > backup_$(date +%Y%m%d_%H%M%S).sql

# Descargar backup a local
scp -P 65002 u329333801@147.79.84.57:~/backup_*.sql ./backups/
```

### Restaurar backup
```bash
mysql -u u329333801_crmuser -p u329333801_crm_bahari < backup_20260224.sql
```

### Ejecutar migración
```bash
mysql -u u329333801_crmuser -p u329333801_crm_bahari < migration_nombre.sql
```

## Logs y Debugging

### Ver logs de errores PHP
```bash
# Tiempo real
tail -f logs/error.log

# Últimas 50 líneas
tail -50 logs/error.log

# Buscar errores específicos
grep "Fatal error" logs/error.log
grep "Warning" logs/error.log
```

### Ver logs de Apache
```bash
tail -50 ~/public_html/error_log
```

### Limpiar logs antiguos
```bash
find logs/ -name "*.log" -type f -mtime +30 -delete
```

## Gestión de Archivos

### Verificar permisos
```bash
ls -la config/
ls -la logs/
```

### Corregir permisos
```bash
# Archivos PHP
find . -type f -name "*.php" -exec chmod 644 {} \;

# Directorios
find . -type d -exec chmod 755 {} \;

# Directorios especiales
chmod 755 logs uploads
```

### Transferir archivos desde local
```bash
# Un archivo
scp -P 65002 archivo.php u329333801@147.79.84.57:/home/u329333801/domains/crm.bahariaqua.com/public_html/

# Directorio completo
scp -r -P 65002 directorio/ u329333801@147.79.84.57:/home/u329333801/domains/crm.bahariaqua.com/public_html/
```

### Descargar archivos a local
```bash
scp -P 65002 u329333801@147.79.84.57:/home/u329333801/domains/crm.bahariaqua.com/public_html/logs/error.log ./
```

## Deployment Manual

### Rsync completo (si falla GitHub Actions)
```bash
rsync -avz --delete \
  --exclude='.git' \
  --exclude='.github' \
  --exclude='logs/*.log' \
  --exclude='config/config.php' \
  --exclude='.env' \
  -e "ssh -p 65002 -o StrictHostKeyChecking=no" \
  ./ u329333801@147.79.84.57:/home/u329333801/domains/crm.bahariaqua.com/public_html/
```

### Rsync de solo vistas (más rápido)
```bash
rsync -avz \
  -e "ssh -p 65002" \
  ./views/ u329333801@147.79.84.57:/home/u329333801/domains/crm.bahariaqua.com/public_html/views/
```

### Rsync de solo un archivo
```bash
rsync -avz \
  -e "ssh -p 65002" \
  ./controllers/UsuarioController.php u329333801@147.79.84.57:/home/u329333801/domains/crm.bahariaqua.com/public_html/controllers/
```

## Verificación del Sitio

### Probar conexión
```bash
curl -I https://crm.bahariaqua.com
```

### Verificar SSL
```bash
openssl s_client -connect crm.bahariaqua.com:443 -servername crm.bahariaqua.com
```

### Test de endpoints
```bash
# Homepage
curl -L https://crm.bahariaqua.com

# Login page
curl https://crm.bahariaqua.com/index.php?c=usuario&a=login
```

## Mantenimiento de GitHub

### Verificar secrets
```bash
# En GitHub: Settings → Secrets and variables → Actions
# Debe tener: SSH_PRIVATE_KEY, SSH_HOST, SSH_PORT, SSH_USER
```

### Reejecutar workflow fallido
```bash
# En GitHub: Actions → [workflow fallido] → Re-run jobs
```

### Ver logs de workflow
```bash
# En GitHub: Actions → Deploy to Hostinger → [seleccionar run]
```

## Rollback

### Opción 1: Revertir último commit
```bash
git revert HEAD
git push origin main
```

### Opción 2: Volver a commit anterior
```bash
git log  # Ver hash del commit anterior
git checkout HASH_DEL_COMMIT
git push origin main
```

### Opción 3: Restaurar desde backup
```bash
# Conectarse por SSH
ssh -p 65002 u329333801@147.79.84.57

# Restaurar base de datos
mysql -u u329333801_crmuser -p u329333801_crm_bahari < backup_anterior.sql

# Restaurar archivos (si tienes backup)
cp -r backup_files/* /home/u329333801/domains/crm.bahariaqua.com/public_html/
```

## Generar Claves

### Clave SSH nueva (si necesitas)
```bash
ssh-keygen -t rsa -b 4096 -C "tu@email.com"
```

### Encryption key para config.php
```bash
openssl rand -base64 32
```

### Password seguro
```bash
openssl rand -base64 16
```

## Monitoring

### Verificar espacio en disco
```bash
df -h
```

### Ver procesos PHP
```bash
ps aux | grep php
```

### Verificar uso de memoria
```bash
free -m
```

### Ver usuarios conectados
```bash
w
last
```

## Troubleshooting Rápido

### Sitio caído
```bash
# 1. Ver logs
tail -50 logs/error.log

# 2. Verificar permisos
ls -la

# 3. Probar conexión BD
mysql -u u329333801_crmuser -p u329333801_crm_bahari -e "SELECT 1"

# 4. Verificar sintaxis PHP
php -l index.php
```

### Deployment fallido
```bash
# 1. Ver logs en GitHub Actions

# 2. Probar conexión SSH manual
ssh -p 65002 u329333801@147.79.84.57

# 3. Verificar secrets en GitHub

# 4. Deployment manual como backup (ver sección Deployment Manual)
```

### Error 500
```bash
# Ver logs completos
tail -100 logs/error.log
tail -100 ~/public_html/error_log

# Verificar sintaxis
find . -name "*.php" -exec php -l {} \;
```

### Error de base de datos
```bash
# Verificar conexión
mysql -u u329333801_crmuser -p u329333801_crm_bahari

# Ver tablas
mysql -u u329333801_crmuser -p u329333801_crm_bahari -e "SHOW TABLES"

# Verificar config.php
cat config/config.php | grep DB_
```

---

**Tip:** Guarda estos comandos en un archivo local para referencia rápida.
