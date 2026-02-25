# 🚀 GUÍA DE DEPLOYMENT A HOSTINGER

## ✅ ARCHIVOS PREPARADOS PARA PRODUCCIÓN

### 1️⃣ `.htaccess` 
Ya configurado para raíz del dominio (`RewriteBase /`)

### 2️⃣ `config/config.production.php`
**Archivo listo con tus credenciales:**
- Base de datos: `u329333801_crm_bahari`
- Usuario: `u329333801_crm_bahari`
- Password: `MNQ+XPV|5jR`
- Dominio: `https://crm.bahariaqua.com`
- Clave de encriptación única generada
- DEBUG_MODE desactivado

---

## 📦 PASOS PARA SUBIR A HOSTINGER

### PASO 1: Preparar archivos localmente

#### A) Hacer backup de tu config actual (opcional):
```powershell
Copy-Item config\config.php config\config.local.backup.php
```

#### B) Crear un commit final:
```powershell
git add .
git commit -m "Configuración lista para producción"
git push origin main
```

### PASO 2: Subir archivos a Hostinger

#### Opción A - Via FTP/SFTP (Recomendado):
1. Abrir FileZilla o cliente FTP de Hostinger
2. Conectar con las credenciales FTP de tu hosting
3. Navegar a la carpeta `public_html` (o la raíz de tu dominio)
4. Subir **TODOS** los archivos EXCEPTO:
   - ❌ `config/config.php` (tu archivo local)
   - ❌ `.git/` (directorio completo)
   - ❌ `logs/*.log` (archivos .log antiguos)
   - ❌ `config/config.local.backup.php` (tu backup)

#### Opción B - Via Git en Hostinger:
```bash
# Conectar por SSH a Hostinger y ejecutar:
cd public_html
git clone https://github.com/davidcaroo/crm-bahari.git .
```

### PASO 3: Configurar archivo config.php en el servidor

**⚠️ IMPORTANTE: Este paso debe hacerse EN EL SERVIDOR (Hostinger), no localmente**

#### Via File Manager de Hostinger:
1. Ir al File Manager de Hostinger
2. Navegar a `public_html/config/`
3. Renombrar `config.production.php` → `config.php`

#### Via SSH:
```bash
cd public_html/config
mv config.production.php config.php
```

### PASO 4: Importar la base de datos

1. Ir a phpMyAdmin en Hostinger
2. Seleccionar la base de datos: `u329333801_crm_bahari`
3. Importar el archivo: `esquema.sql`
4. Verificar que las tablas se crearon correctamente

### PASO 5: Configurar permisos

#### Via File Manager:
Dar permisos de escritura al directorio `logs/`:
- Clic derecho en carpeta `logs/`
- Permisos → `755` o `775`

#### Via SSH:
```bash
cd public_html
chmod 755 logs/
chmod 644 logs/.gitignore
```

### PASO 6: Verificar SSL/HTTPS

1. En el panel de Hostinger, ir a: **Dominios → crm.bahariaqua.com**
2. Activar **SSL gratuito** (Let's Encrypt)
3. Forzar HTTPS (redirección automática)

---

## 🔍 VERIFICACIONES POST-DEPLOYMENT

### ✅ Checklist de pruebas:

1. **Acceso al sitio:**
   - [ ] Abrir: https://crm.bahariaqua.com
   - [ ] Debe redirigir a /usuarios/login

2. **Conexión a base de datos:**
   - [ ] No debe mostrar errores de conexión
   - [ ] Si hay error, verificar credenciales en config.php

3. **Login:**
   - [ ] Probar login con usuario de prueba
   - [ ] Verificar que redirija al dashboard

4. **Funcionalidades clave:**
   - [ ] Dashboard carga correctamente
   - [ ] Gráficas se muestran (Chart.js)
   - [ ] Íconos aparecen (Bootstrap Icons)
   - [ ] Búsqueda de empresas funciona
   - [ ] Exportar trazabilidad a CSV funciona

5. **Recursos estáticos:**
   - [ ] CSS se carga (Bootstrap)
   - [ ] JavaScript funciona (Chart.js)
   - [ ] Imágenes se muestran

### 🔧 Solución de problemas comunes:

#### Problema: Error 500 (Internal Server Error)
**Solución:**
```bash
# Verificar logs de error:
tail -f logs/error.log

# O revisar error_log de Hostinger en:
# File Manager → error_log
```

#### Problema: "Database connection failed"
**Solución:**
1. Ir a phpMyAdmin → Verificar que la base de datos existe
2. Verificar en config.php que `DB_HOST` sea `'localhost'`
3. Si no funciona, probar cambiar a `'127.0.0.1'`

#### Problema: CSS/JS no cargan (404)
**Solución:**
1. Verificar que `.htaccess` existe en la raíz
2. Verificar que `RewriteBase /` está correcto
3. Verificar permisos de archivos: `chmod 644 *.css *.js`

#### Problema: Redirección infinita en login
**Solución:**
1. Verificar que `BASE_URL` en config.php sea: `'https://crm.bahariaqua.com'`
2. Verificar que no tenga `/` al final

---

## 📝 NOTAS IMPORTANTES

### 🔒 Seguridad:
- ✅ La clave de encriptación es única (generada aleatoriamente)
- ✅ DEBUG_MODE está desactivado en producción
- ✅ config.php está en .gitignore (no se sube a GitHub)
- ✅ Las sesiones usan cookies seguras (HTTPS only)

### ⚙️ Configuración:
- ✅ Zona horaria: America/Bogota (hora de Colombia)
- ✅ Límite de subida: 10MB
- ✅ Timeout de ejecución: 300 segundos
- ✅ Logs de error se guardan en: `logs/error.log`

### 🌐 URLs importantes:
- Sitio: https://crm.bahariaqua.com
- Login: https://crm.bahariaqua.com/usuarios/login
- Panel Hostinger: https://hpanel.hostinger.com
- phpMyAdmin: (disponible en el panel de Hostinger)

---

## 🆘 SOPORTE

Si encuentras problemas:
1. Revisar logs: `logs/error.log`
2. Revisar error_log de Hostinger (File Manager)
3. Verificar en phpMyAdmin que la BD tenga datos

**Credenciales de DB guardadas en:** `config/config.production.php`

---

## ✅ TODO LISTO

El CRM está configurado y listo para subir a Hostinger. Solo falta:
1. Subir archivos via FTP/SSH
2. Renombrar config.production.php → config.php EN EL SERVIDOR
3. Importar base de datos
4. ¡Probar!

**¡Buena suerte con el deployment! 🚀**
