# Guía de Deployment - CRM Bahari Aqua

## 🚀 Configuración Inicial del CI/CD

### Prerrequisitos
- Repositorio de GitHub configurado
- Acceso SSH a Hostinger
- Credenciales de base de datos en Hostinger

### Paso 1: Configurar GitHub Secrets

Ve a tu repositorio en GitHub:
**Settings → Secrets and variables → Actions → New repository secret**

Agrega los siguientes secrets:

#### SSH_PRIVATE_KEY
```
-----BEGIN OPENSSH PRIVATE KEY-----
[Tu clave privada SSH aquí]
-----END OPENSSH PRIVATE KEY-----
```
**Nota:** Esta es la clave privada que corresponde a tu clave pública SSH. Nunca compartas esta clave.

#### SSH_HOST
```
147.79.84.57
```

#### SSH_PORT
```
65002
```

#### SSH_USER
```
u329333801
```

### Paso 2: Configurar Hostinger

#### 2.1 Agregar Clave SSH Pública

1. Accede al panel de Hostinger
2. Ve a **Advanced → SSH Access → Manage SSH Keys**
3. Agrega esta clave pública:
```
ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQCft0s9WpntXOMcrV1feZ6SJoQWEwplVvjr3JOhp23YGOVwW/5V/dahjm24BWt9EF2DB8vDTXxdOfQYQ2YydtSHIsAmgf+ujOWMUKHfx1E9PkjW6ebP/m6TCjdsL57rp6Qg+YPBtH2tc6QySTsTB/Gxbp5XX6BQOsZa/Qq2cs/bJNhD8fY1iLg0dmyeh/JfScKaatjgCkWFRH7XVBE9NfvGYK6PggJBiUuzdFLAzyPsEfEZ6FLJTfOQAXD5iE9FuecSDq6Lk3qWP9lUDzLcm0PlEyTebNuTXGN845zsgZlFTmXcIv4jdojs7VqQk72FIYj987FSFc8mcqmlWmDLtxTdh1UK4T3lQR0M0WtovA576HpNjuXHkbccUKOK7NyQus1PVFCbnNq08TsJHaU2qh9nv+n74puTzcLO/Y0VMdrAOpaClwCiLFrQHXKFE01hnH6WnXfWfRiIEmhvyQMPv82hbReFYShUpdYsfsSCTs2zjazPJzXZndCvaHN29uAlHAc= u329333801@br-asc-web1664.main-hosting.eu
```

#### 2.2 Configurar Estructura de Directorios

Conéctate por SSH:
```bash
ssh -p 65002 u329333801@147.79.84.57
```

Crea la estructura necesaria:
```bash
cd /home/u329333801/domains/crm.bahariaqua.com/public_html/
mkdir -p logs uploads config
chmod 755 logs uploads
```

#### 2.3 Crear Base de Datos

1. En el panel de Hostinger, ve a **Databases → MySQL Databases**
2. Crea una nueva base de datos: `u329333801_crm_bahari`
3. Crea un usuario: `u329333801_crmuser`
4. Asigna permisos completos al usuario sobre la base de datos
5. Anota la contraseña generada

#### 2.4 Importar Esquema SQL

Opción A - Desde phpMyAdmin:
1. Abre phpMyAdmin desde el panel de Hostinger
2. Selecciona la base de datos creada
3. Ve a "Import"
4. Sube y ejecuta en este orden:
   - `esquema.sql`
   - `esquema_crm.sql`
   - `esquema_configuracion.sql`
   - `migration_*.sql` (todos los archivos de migración)

Opción B - Desde SSH:
```bash
cd /home/u329333801/domains/crm.bahariaqua.com/public_html/
mysql -u u329333801_crmuser -p u329333801_crm_bahari < esquema.sql
mysql -u u329333801_crmuser -p u329333801_crm_bahari < esquema_crm.sql
# ... continuar con los demás archivos
```

#### 2.5 Configurar Archivo de Producción

```bash
cd /home/u329333801/domains/crm.bahariaqua.com/public_html/
cp config/config.production.php.example config/config.php
nano config/config.php
```

Actualiza estos valores:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u329333801_crm_bahari');
define('DB_USER', 'u329333801_crmuser');
define('DB_PASS', 'TU_PASSWORD_REAL_AQUI');

define('BASE_URL', 'https://crm.bahariaqua.com');

// Genera una nueva clave de cifrado
define('ENCRYPTION_KEY', 'CLAVE_ALEATORIA_32_CARACTERES');

define('DEBUG_MODE', false);

// Configuración SMTP de Hostinger
define('SMTP_HOST', 'smtp.hostinger.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'noreply@bahariaqua.com');
define('SMTP_PASS', 'TU_PASSWORD_EMAIL');
```

Guarda con `Ctrl+O`, `Enter`, `Ctrl+X`

### Paso 3: Configurar SSL/HTTPS

1. En el panel de Hostinger, ve a **Security → SSL**
2. Selecciona el dominio `crm.bahariaqua.com`
3. Activa "Force HTTPS" para redirigir HTTP a HTTPS

### Paso 4: Primer Deployment

#### Opción A: Deployment Automático
```bash
# En tu máquina local
git add .
git commit -m "Initial deployment setup"
git push origin main
```

Ve a **Actions** en GitHub y monitorea el workflow.

#### Opción B: Deployment Manual
```bash
# Desde tu máquina local
rsync -avz --delete \
  --exclude='.git' \
  --exclude='logs/*.log' \
  --exclude='config/config.php' \
  -e "ssh -p 65002" \
  ./ u329333801@147.79.84.57:/home/u329333801/domains/crm.bahariaqua.com/public_html/
```

### Paso 5: Verificar Funcionamiento

1. Abre https://crm.bahariaqua.com
2. Verifica que la página de login cargue correctamente
3. Intenta iniciar sesión con un usuario de prueba
4. Revisa los logs si hay errores:
```bash
ssh -p 65002 u329333801@147.79.84.57
tail -f /home/u329333801/domains/crm.bahariaqua.com/public_html/logs/error.log
```

## 🔄 Workflow de Desarrollo

### Desarrollo Local
```bash
# Trabajar en rama de desarrollo
git checkout -b feature/nueva-funcionalidad

# Hacer cambios...
git add .
git commit -m "Descripción de cambios"
git push origin feature/nueva-funcionalidad

# Crear Pull Request en GitHub
# Después de revisión, hacer merge a main
```

### Deployment Automático
- Cada push a `main` dispara el workflow automáticamente
- GitHub Actions ejecuta:
  1. Checkout del código
  2. Configuración de SSH
  3. Rsync a Hostinger
  4. Post-deployment tasks
  5. Notificación de resultado

### Monitorear Deployment
```bash
# Ver logs en tiempo real
ssh -p 65002 u329333801@147.79.84.57
tail -f /home/u329333801/domains/crm.bahariaqua.com/public_html/logs/deployment.log
```

## 🛠️ Troubleshooting

### Error: "Host key verification failed"
```bash
# Agregar host a known_hosts
ssh-keyscan -p 65002 147.79.84.57 >> ~/.ssh/known_hosts
```

### Error: "Permission denied"
```bash
# Verificar permisos de la clave SSH
chmod 600 ~/.ssh/id_rsa
```

### Error: "Database connection failed"
- Verifica credenciales en `config/config.php`
- Confirma que el usuario tiene permisos sobre la base de datos
- Prueba la conexión desde SSH:
```bash
mysql -u u329333801_crmuser -p -h localhost u329333801_crm_bahari
```

### Sitio muestra error 500
```bash
# Ver logs de PHP
tail -50 /home/u329333801/domains/crm.bahariaqua.com/public_html/logs/error.log

# Ver logs de Apache
tail -50 ~/public_html/error_log
```

## 📊 Mantenimiento

### Backup de Base de Datos
```bash
# Crear backup
mysqldump -u u329333801_crmuser -p u329333801_crm_bahari > backup_$(date +%Y%m%d).sql

# Restaurar backup
mysql -u u329333801_crmuser -p u329333801_crm_bahari < backup_20260224.sql
```

### Limpiar Logs Antiguos
```bash
find logs/ -name "*.log" -type f -mtime +30 -delete
```

### Actualizar Dependencias
Si usas Composer en el futuro:
```bash
cd /home/u329333801/domains/crm.bahariaqua.com/public_html/
composer update --no-dev
```

## 🔐 Seguridad

### Checklist de Seguridad
- [ ] `DEBUG_MODE` está en `false` en producción
- [ ] Credenciales de BD no están en el repositorio
- [ ] HTTPS/SSL está activo y forzado
- [ ] Permisos de archivos correctos (644 para PHP, 755 para directorios)
- [ ] Clave SSH privada solo en GitHub Secrets
- [ ] Logs no expuestos públicamente
- [ ] `config.php` excluido de git y rsync

### Rotar Credenciales
1. Generar nuevas contraseñas en Hostinger
2. Actualizar `config/config.php` en el servidor
3. Actualizar secrets en GitHub si es necesario
4. Probar funcionamiento

## 📞 Soporte

Si encuentras problemas:
1. Revisa logs: `logs/error.log` y `logs/deployment.log`
2. Verifica el workflow en GitHub Actions
3. Consulta la documentación de Hostinger
4. Contacta al equipo de desarrollo

---

**Última actualización:** 24 de febrero de 2026
