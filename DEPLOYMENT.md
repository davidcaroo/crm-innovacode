# 🚀 DEPLOYMENT A HOSTINGER

## ⚠️ ARCHIVOS QUE CAMBIAR EN EL SERVIDOR

### 1. config.php
Renombrar: `config.production.php` → `config.php`

### 2. .htaccess
Renombrar: `.htaccess.production` → `.htaccess`

**O editar manualmente el .htaccess en el servidor y cambiar:**
```apache
RewriteBase /crm-php.com/
```
**Por:**
```apache
RewriteBase /
```

---

## 📦 PASOS

1. **Subir archivos** via FTP a `public_html/`
2. **Renombrar archivos:**
   - `config.production.php` → `config.php`
   - `.htaccess.production` → `.htaccess`
3. **Importar base de datos** en phpMyAdmin: `esquema.sql`
4. **Ejecutar script de corrección:** `verificar-datos-produccion.sql` en phpMyAdmin
5. **Verificar permisos** de `logs/` (755)
6. **Activar SSL** en Hostinger
7. **Probar:** https://crm.bahariaqua.com

---

## 🔧 PROBLEMA: SELECT VACÍOS (Sin opciones seleccionadas)

Si en producción no se ven las opciones seleccionadas en los formularios de edición (usuarios, empresas), es porque **la base de datos tiene campos NULL o vacíos**.

### Solución:

1. Ir a **phpMyAdmin** en Hostinger
2. Seleccionar base de datos: `u329333801_crm_bahari`
3. Ir a pestaña **SQL**
4. Copiar y pegar el contenido del archivo `verificar-datos-produccion.sql`
5. Hacer clic en **Continuar**

Esto corregirá:
- ✅ Usuarios sin rol → asigna 'usuario'
- ✅ Usuarios sin estado → asigna 'activo'  
- ✅ Empresas sin etapa → asigna 'prospecto'

---

## 🔍 SI HAY ERROR 500

Subir `diagnostico.php` al servidor y acceder:
https://crm.bahariaqua.com/diagnostico.php

**¡Eliminar diagnostico.php después de usarlo!**

---

## ✅ VERIFICACIÓN POST-DEPLOYMENT

- [ ] Login funciona
- [ ] Dashboard carga correctamente
- [ ] Formularios de edición muestran opciones seleccionadas
- [ ] Búsqueda de empresas funciona
- [ ] Exportar CSV funciona
- [ ] CSS y JS cargan correctamente
