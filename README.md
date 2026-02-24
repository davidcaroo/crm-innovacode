# CRM - Sistema de Gestión de Clientes

Sistema de gestión de relaciones con clientes (CRM) desarrollado en PHP con MySQL, que incluye dashboard general y vista detallada por cliente.

## Características

- 📊 Dashboard general con estadísticas
- 👥 Gestión de clientes
- 📈 Visualización de datos por cliente
- 🎯 Interfaz intuitiva y responsive
- 🔍 Búsqueda y filtrado de clientes

## Requisitos Previos

- **PHP 7.0+** (recomendado 7.4 o superior)
- **MySQL 5.7+** o **MariaDB 10.2+**
- **Apache 2.4+** (incluido en XAMPP)
- **XAMPP** (opcional, para desarrollo local)

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
