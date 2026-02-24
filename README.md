# CRM - Sistema de Gestión de Clientes

Sistema de gestión de relaciones con clientes (CRM) desarrollado en PHP con MySQL, que incluye dashboard general y vista detallada por cliente.

## Características

- 📊 Dashboard general con estadísticas
- 👥 Gestión de clientes y empresas
- 📈 Visualización de datos por cliente
- 🎯 Interfaz intuitiva y responsive
- 🔍 Búsqueda y filtrado de clientes
- 🔐 Sistema de autenticación con roles
- 📧 Notificaciones por email
- 🔄 CI/CD automático con GitHub Actions

## Requisitos Previos

- **PHP 7.4+** (recomendado 8.0 o superior)
- **MySQL 5.7+** o **MariaDB 10.2+**
- **Apache 2.4+** (incluido en XAMPP)
- **XAMPP** (opcional, para desarrollo local)
- **Composer** (opcional, para dependencias)

## Instalación

### 1. Preparar el entorno

En Windows con XAMPP:
- Copia todos los archivos a `C:\xampp\htdocs\crm-php.com`
- Si la carpeta no existe, créala manualmente

En Linux/macOS:
- Copia los archivos al directorio raíz web de tu servidor (generalmente `/var/www/html/`)

### 2. Configurar la base de datos

- Abre phpMyAdmin (http://localhost/phpmyadmin) o usa la consola MySQL
- Crea una nueva base de datos con el nombre deseado
- Importa el archivo `esquema.sql`:
  - En phpMyAdmin: selecciona la BD → Importar → selecciona `esquema.sql`
  - En consola: `mysql -u root -p nombre_bd < esquema.sql`

### 3. Configurar credenciales

Edita el archivo `__funciones.php`:
```php
// Busca la función obtenerBD() y actualiza:
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_datos = 'nombre_bd';
```

### 4. Configurar zona horaria

En el mismo archivo `__funciones.php`, ajusta la zona horaria según tu ubicación:
```php
date_default_timezone_set('America/Mexico_City'); // Cambia según tu zona
```

### 5. Acceder al proyecto

Abre tu navegador y ve a:
- **Windows con XAMPP**: http://localhost/crm-php.com
- **Linux/macOS**: http://tudominio/crm-php.com (o la ruta configurada)

## Estructura de Carpetas

```
crm-php.com/
├── index.php          # Página principal
├── __funciones.php    # Funciones y configuración
├── esquema.sql        # Estructura de la base de datos
├── css/               # Estilos
├── js/                # Scripts
├── img/               # Imágenes e iconos
└── README.md          # Este archivo
```

## Solución de Problemas

**Error de conexión a base de datos:**
- Verifica que MySQL está ejecutándose
- Comprueba las credenciales en `__funciones.php`
- Asegúrate de que la base de datos existe

**Página en blanco:**
- Revisa los logs de error de PHP
- Comprueba que la zona horaria está correcta
- Verifica los permisos de los archivos

**Ruta incorrecta:**
- Si el proyecto está en otra carpeta, actualiza las rutas en los archivos

## Deployment Automático (CI/CD)

Este proyecto está configurado para despliegue automático a Hostinger mediante GitHub Actions.

### Configuración Inicial

#### 1. Secrets de GitHub

Ve a **Settings > Secrets and variables > Actions** en tu repositorio y configura:

| Secret Name | Valor | Descripción |
|-------------|-------|-------------|
| `SSH_PRIVATE_KEY` | Clave privada SSH | La clave privada correspondiente a la pública configurada en Hostinger |
| `SSH_HOST` | `147.79.84.57` | IP del servidor Hostinger |
| `SSH_PORT` | `65002` | Puerto SSH del servidor |
| `SSH_USER` | `u329333801` | Usuario SSH de Hostinger |

#### 2. Configuración en Hostinger (Primera vez)

**a) Agregar clave SSH pública:**
```bash
# En Hostinger, ve a: Advanced > SSH Access > Manage SSH Keys
# Agrega la clave pública proporcionada
```

**b) Crear el archivo de configuración de producción:**
```bash
# Conéctate por SSH a Hostinger
ssh -p 65002 u329333801@147.79.84.57

# Navega al directorio del proyecto
cd /home/u329333801/domains/crm.bahariaqua.com/public_html/

# Copia el archivo de ejemplo
cp config/config.production.php.example config/config.php

# Edita el archivo con las credenciales reales
nano config/config.php
```

**c) Configurar base de datos:**
- Crea la base de datos en phpMyAdmin de Hostinger
- Importa el esquema SQL completo:
  - `esquema.sql`
  - `esquema_crm.sql`
  - `migration_*.sql` (en orden cronológico)

**d) Configurar permisos:**
```bash
chmod 755 logs uploads
chmod 644 config/config.php
```

### Flujo de Deployment

1. Haz cambios en tu código local
2. Commit y push a la rama `main` o `master`:
   ```bash
   git add .
   git commit -m "Descripción de cambios"
   git push origin main
   ```
3. GitHub Actions se ejecuta automáticamente
4. El código se sincroniza vía rsync a Hostinger
5. Se ejecutan tareas post-deployment
6. El sitio en `https://crm.bahariaqua.com` se actualiza

### Monitorear Deployment

- Ve a **Actions** en tu repositorio de GitHub
- Revisa los logs del workflow `Deploy to Hostinger`
- Verifica que todos los steps se completen con éxito ✅

### Archivos Excluidos del Deployment

El workflow **NO** sobrescribe estos archivos en producción:
- `config/config.php` (credenciales de producción)
- `logs/*.log` (logs del servidor)
- `.env` (variables de entorno)
- `.git/` (historial de git)
- `node_modules/`, `vendor/` (dependencias)

### Rollback en Caso de Error

Si un deployment causa problemas:

```bash
# Opción 1: Revertir el último commit y hacer push
git revert HEAD
git push origin main

# Opción 2: Conectarse por SSH y restaurar desde backup
ssh -p 65002 u329333801@147.79.84.57
cd /home/u329333801/domains/crm.bahariaqua.com/
# Restaurar archivos manualmente
```

### Troubleshooting

**Error: "Permission denied (publickey)"**
- Verifica que la clave privada en GitHub Secrets sea correcta
- Asegúrate de que la clave pública esté agregada en Hostinger

**Error: "rsync: failed to connect to..."**
- Verifica que el servidor SSH esté activo en Hostinger
- Confirma el puerto (65002) y la IP (147.79.84.57)

**Error: "config.php not found"**
- Crea manualmente el archivo `config/config.php` en el servidor
- Usa `config.production.php.example` como plantilla

**Deployment exitoso pero sitio no funciona:**
- Verifica la base de datos en Hostinger
- Revisa los logs: `logs/error.log`
- Confirma que los permisos de directorios sean correctos

### Mantenimiento

**Revisar logs de deployment:**
```bash
ssh -p 65002 u329333801@147.79.84.57
cat /home/u329333801/domains/crm.bahariaqua.com/public_html/logs/deployment.log
```

**Limpiar logs antiguos:**
```bash
find logs/ -name "*.log" -type f -mtime +30 -delete
```

**Ejecutar migraciones manualmente:**
```bash
# Desde phpMyAdmin o consola MySQL
mysql -u u329333801_crmuser -p u329333801_crm_bahari < migration_nombre.sql
```
