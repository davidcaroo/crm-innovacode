<div align="center">

# 🚀 CRM Innovacode

**Sistema de Gestión de Relaciones Comerciales**

Un CRM moderno y completo para la gestión integral de equipos comerciales, pipeline de ventas y trazabilidad de actividades.

[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-4.6-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![jQuery](https://img.shields.io/badge/jQuery-3.6-0769AD?style=for-the-badge&logo=jquery&logoColor=white)](https://jquery.com)
[![License](https://img.shields.io/badge/License-Proprietary-red?style=for-the-badge)](#-licencia)

</div>

---

## 📋 Descripción

CRM Innovacode es una plataforma web diseñada para centralizar y optimizar la operación comercial de equipos de ventas. Permite gestionar empresas, contactos, oportunidades y el ciclo completo de ventas desde la prospección hasta el cierre, con trazabilidad completa de cada interacción.

## ✨ Características Principales

| Módulo | Descripción |
|--------|-------------|
| 📊 **Dashboard Analítico** | Panel de control con KPIs en tiempo real, métricas de rendimiento y gráficos interactivos |
| 🏢 **Gestión de Empresas** | CRUD completo, importación masiva por Excel, búsqueda avanzada y filtrado |
| 🔄 **Pipeline Comercial** | Visualización Kanban del embudo de ventas con drag & drop entre etapas |
| 📝 **Trazabilidad** | Historial completo de actividades comerciales por empresa y usuario |
| 👥 **Contactos** | Gestión de personas de contacto vinculadas a cada empresa |
| 💰 **Cierre de Ventas** | Registro y seguimiento de ventas ganadas con métricas de conversión |
| 📈 **Reportes** | Exportación a Excel con reportes globales e individuales por comercial |
| 🔔 **Notificaciones** | Sistema de alertas en tiempo real para eventos del CRM |
| 👤 **Gestión de Usuarios** | Roles (superadmin, admin, usuario), impersonación y control de acceso |
| ⚙️ **Configuración** | Panel de ajustes, integración SMTP y personalización del sistema |
| 🛟 **Soporte** | Centro de ayuda integrado para los usuarios |

## 🛠️ Stack Tecnológico

<table>
<tr>
<td align="center" width="120">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" width="48" height="48" alt="PHP" />
<br><strong>PHP 8.0+</strong>
<br><sub>Backend</sub>
</td>
<td align="center" width="120">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg" width="48" height="48" alt="MySQL" />
<br><strong>MySQL 8.0</strong>
<br><sub>Base de datos</sub>
</td>
<td align="center" width="120">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-original.svg" width="48" height="48" alt="Bootstrap" />
<br><strong>Bootstrap 4.6</strong>
<br><sub>UI Framework</sub>
</td>
<td align="center" width="120">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/jquery/jquery-original.svg" width="48" height="48" alt="jQuery" />
<br><strong>jQuery 3.6</strong>
<br><sub>Interactividad</sub>
</td>
<td align="center" width="120">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" width="48" height="48" alt="JavaScript" />
<br><strong>JavaScript</strong>
<br><sub>Frontend</sub>
</td>
<td align="center" width="120">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/apache/apache-original.svg" width="48" height="48" alt="Apache" />
<br><strong>Apache</strong>
<br><sub>Servidor web</sub>
</td>
</tr>
</table>

**Librerías adicionales:**

- 🎨 [SB Admin 2](https://startbootstrap.com/theme/sb-admin-2) — Plantilla administrativa
- 📊 [Chart.js](https://www.chartjs.org/) — Gráficos interactivos
- 📋 [DataTables](https://datatables.net/) — Tablas dinámicas con paginación
- 🍬 [SweetAlert2](https://sweetalert2.github.io/) — Alertas y confirmaciones elegantes
- 🔤 [Font Awesome 5](https://fontawesome.com/) — Iconografía profesional
- 🔡 [Google Fonts (Nunito)](https://fonts.google.com/specimen/Nunito) — Tipografía del sistema

## 🏗️ Arquitectura

El proyecto sigue un patrón **MVC (Modelo-Vista-Controlador)** con routing limpio basado en URL amigables:

```
crm-php.com/
├── config/                 # Configuración del sistema y base de datos
│   ├── config.php          # Config local (excluido de git)
│   ├── config.production.php # Config de producción (template)
│   ├── Database.php        # Singleton de conexión PDO
│   └── routes.php          # Definición de rutas del sistema
├── controllers/            # Controladores (lógica de negocio)
│   ├── BaseController.php  # Controlador base con helpers comunes
│   ├── DashboardController.php
│   ├── EmpresaController.php
│   ├── ReporteController.php
│   ├── TrazabilidadController.php
│   └── ...
├── core/                   # Núcleo del framework
│   └── Router.php          # Router con URL amigables (SEO-friendly)
├── models/                 # Modelos de datos (acceso a BD)
│   ├── BaseModel.php       # Modelo base con métodos CRUD genéricos
│   ├── Empresa.php
│   ├── Trazabilidad.php
│   ├── Reporte.php
│   └── ...
├── views/                  # Vistas (templates PHP)
│   ├── layouts/            # Encabezado, pie y sidebar compartidos
│   ├── dashboard/
│   ├── empresas/
│   ├── reportes/
│   └── ...
├── public/                 # Assets públicos (CSS, JS, imágenes)
├── logs/                   # Logs de errores (excluido de git)
├── index.php               # Entry point del sistema
├── .htaccess               # Reglas de rewriting (Apache)
└── .htaccess.production    # Reglas optimizadas para producción
```

## 🚀 Instalación

### Requisitos previos

| Software | Versión mínima |
|----------|---------------|
| PHP | 8.0+ |
| MySQL / MariaDB | 5.7+ / 10.2+ |
| Apache | 2.4+ con `mod_rewrite` |

### Paso a paso

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/crm-bahari.git
   cd crm-bahari
   ```

2. **Configurar la base de datos**
   - Crear una base de datos MySQL
   - Importar el esquema desde `crm_db-production.sql`

3. **Configurar el entorno**
   - Copiar `config/config.production.php` como `config/config.php`
   - Editar las credenciales de base de datos, URL base y claves de cifrado

4. **Configurar permisos**
   ```bash
   mkdir -p logs
   chmod 755 logs
   ```

5. **Configurar Apache**
   - Asegurar que `mod_rewrite` esté habilitado
   - El archivo `.htaccess` ya incluye las reglas necesarias

6. **Acceder al sistema**
   - Abrir el navegador en la URL configurada
   - Iniciar sesión con las credenciales de administrador

## 🔐 Seguridad

- ✅ Autenticación basada en sesiones con protección CSRF
- ✅ Contraseñas hasheadas con `bcrypt` (PASSWORD_DEFAULT)
- ✅ Prepared statements (PDO) en todas las consultas SQL
- ✅ Sanitización de entradas con `htmlspecialchars`
- ✅ Cookies de sesión `HttpOnly`, `Secure` y `SameSite=Strict`
- ✅ Cifrado AES-256-CBC para datos sensibles (claves SMTP, API keys)
- ✅ Manejo de errores sin exposición de stack traces en producción
- ✅ Control de acceso basado en roles (RBAC)

## 🌐 Deployment

El sistema está preparado para despliegue en servidores con hosting compartido (como Hostinger) o servidores dedicados. Consultar `DEPLOYMENT.md` para instrucciones detalladas.

**Entornos soportados:**
- 🖥️ **Local**: XAMPP / WAMP / MAMP
- ☁️ **Producción**: Hostinger, cPanel, VPS con Apache

## 📄 Licencia

Este proyecto es **software propietario**. Todos los derechos reservados.

```
Copyright (c) 2026 David Caro / Innovacode Tech

Se prohíbe la copia, modificación, distribución o uso de este software
sin autorización expresa por escrito del autor.

El acceso al código fuente no implica licencia de uso.
```

## 👨‍💻 Autor

<table>
<tr>
<td align="center">
<strong>David Caro</strong>
<br>
<a href="https://innovacode.click">🌐 innovacode.click</a>
<br>
<sub>Full Stack Developer & Tech Lead</sub>
</td>
</tr>
</table>

---

<div align="center">

**Desarrollado con ❤️ por [Innovacode Tech](https://innovacode.click)**

<sub>CRM Innovacode v2.0.0 · 2026</sub>

</div>
