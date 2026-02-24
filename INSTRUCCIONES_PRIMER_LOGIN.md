# Instrucciones de Implementación: Cambio Obligatorio de Contraseña

## 📋 Resumen de Cambios

Se ha implementado un sistema de seguridad que:
1. **Genera contraseñas temporales automáticas** al crear usuarios (los admins ya no las ingresan manualmente)
2. **Envía emails automáticos** con las credenciales a los nuevos usuarios
3. **Fuerza el cambio de contraseña** en el primer inicio de sesión
4. **Protege todas las rutas** hasta que el usuario complete el cambio obligatorio

---

## 🚀 Pasos para Activar

### 1. Ejecutar la Migración SQL

Abre **phpMyAdmin** o tu cliente MySQL y ejecuta el archivo:

```
migration_primer_login.sql
```

Este script agregará los siguientes campos a la tabla `usuarios`:
- `estado` (activo/inactivo)
- `primer_login` (1 = debe cambiar contraseña, 0 = ya cambió)
- `ultimo_cambio_password` (timestamp del último cambio)

**Importante:** Los usuarios existentes NO serán forzados a cambiar su contraseña (se establece `primer_login = 0` automáticamente).

### 2. Verificar Configuración SMTP

Para que funcione el envío de emails con credenciales:

1. Ve a: `http://localhost/crm-php.com/configuracion`
2. Configura los datos SMTP (Gmail, Outlook, etc.)
3. Haz clic en **"Probar conexión SMTP"** para verificar
4. Si el test es exitoso, recibirás un correo de confirmación

**Ejemplo de configuración Gmail:**
- SMTP Host: `smtp.gmail.com`
- SMTP Port: `587`
- SMTP User: `tucorreo@gmail.com`
- SMTP Pass: `[contraseña de aplicación]`

> **Nota:** Gmail requiere una "contraseña de aplicación" si tienes 2FA activado.  
> Genérala en: https://myaccount.google.com/apppasswords

---

## 🧪 Probar el Sistema

### Paso 1: Crear un nuevo usuario

1. Como **admin** o **superadmin**, ve a: `http://localhost/crm-php.com/usuario/lista`
2. Clic en **"Nuevo Usuario"**
3. Completa:
   - Nombre: `Test Usuario`
   - Email: `test@example.com` (usa un email real que puedas revisar)
   - Rol: `Usuario`
4. **NO** verás el campo de contraseña (se genera automáticamente)
5. Clic en **"Crear Usuario"**

**Resultado esperado:**
- Verás una notificación verde: *"El usuario ha sido creado y recibirá un correo con sus credenciales"*
- El nuevo usuario recibirá un email con:
  - Su usuario (email)
  - Contraseña temporal (12 caracteres alfanuméricos)
  - Botón para iniciar sesión

### Paso 2: Probar el primer login

1. Cierra sesión (o usa navegador incógnito)
2. Ve a: `http://localhost/crm-php.com/usuario/login`
3. Ingresa:
   - Usuario: `test@example.com`
   - Contraseña: `[la contraseña temporal del email]`
4. Clic en **"Iniciar Sesión"**

**Resultado esperado:**
- En lugar de ir al dashboard, serás redirigido a una pantalla de **"Cambio de Contraseña Obligatorio"**
- Esta pantalla tiene un diseño especial (fullscreen, sin sidebar) y no podrás salir sin cambiar la contraseña

### Paso 3: Cambiar la contraseña

1. En el formulario de cambio obligatorio:
   - **Contraseña temporal actual:** `[la del email]`
   - **Nueva contraseña:** `MiNuevaPassword123`
   - **Confirmar nueva contraseña:** `MiNuevaPassword123`
2. Observa el medidor de fortaleza (débil/aceptable/buena/excelente)
3. Clic en **"Cambiar Contraseña y Continuar"**

**Resultado esperado:**
- Verás una notificación verde: *"¡Contraseña actualizada! Tu contraseña ha sido cambiada exitosamente."*
- Serás redirigido al **Dashboard** automáticamente
- En la BD, `primer_login` cambió a `0` y `ultimo_cambio_password` tiene la fecha actual

### Paso 4: Verificar segundo login

1. Cierra sesión
2. Inicia sesión nuevamente con:
   - Usuario: `test@example.com`
   - Contraseña: `MiNuevaPassword123` (la nueva)
3. Clic en **"Iniciar Sesión"**

**Resultado esperado:**
- Esta vez SÍ irás directo al dashboard, sin pasar por el cambio obligatorio
- El sistema ya reconoce que cambiaste la contraseña (`primer_login = 0`)

---

## 🔐 Flujo de Seguridad

```
ADMIN crea usuario
    ↓
Sistema genera password temporal (12 caracteres)
    ↓
Email enviado al nuevo usuario
    ↓
Usuario inicia sesión con password temporal
    ↓
Sistema detecta primer_login = 1
    ↓
Redirige a pantalla de cambio obligatorio
    ↓
Usuario cambia la contraseña
    ↓
BD actualiza: primer_login = 0, ultimo_cambio_password = NOW()
    ↓
Usuario accede al Dashboard
    ↓
En futuros logins, NO se fuerza cambio
```

---

## 📁 Archivos Modificados/Creados

### Nuevos archivos:
- `migration_primer_login.sql` - Migración de BD
- `views/usuarios/cambiar_password_obligatorio.php` - Vista de cambio obligatorio
- `INSTRUCCIONES_PRIMER_LOGIN.md` - Este archivo

### Archivos modificados:
- `models/Usuario.php`
  - Método `crear()` ahora acepta parámetro `$primerLogin`
  - Método `cambiarPassword()` actualiza `primer_login` y `ultimo_cambio_password`
  - Nuevo método `generarPasswordTemporal()` estático
  
- `models/Mailer.php`
  - Nuevo método `enviarCredencialesNuevoUsuario()` con template HTML profesional
  
- `controllers/UsuarioController.php`
  - `login()`: Intercepta `primer_login = 1` y redirige a cambio obligatorio
  - `guardarUsuario()`: Genera password automática y envía email
  - Nuevos métodos: `cambiarPasswordObligatorio()` y `procesarCambioObligatorio()`
  
- `views/usuarios/crear.php`
  - Eliminado campo de contraseña manual
  - Agregado mensaje informativo sobre contraseña automática
  
- `views/layouts/pie.php`
  - Nuevos mensajes SweetAlert para `user_created` y `password_changed`
  
- `index.php`
  - Middleware que protege todas las rutas si `$_SESSION['cambio_password_obligatorio'] = true`

---

## ⚠️ Notas Importantes

1. **Usuarios existentes no afectados:** La migración establece `primer_login = 0` para usuarios actuales
2. **Email obligatorio:** Si SMTP no está configurado, los usuarios se crearán pero NO recibirán el email (tendrás que enviárselo manualmente)
3. **Contraseñas seguras:** El generador crea contraseñas de 12 caracteres con mayúsculas, minúsculas y números
4. **Sin escape:** El usuario NO puede saltar el cambio obligatorio, todas las rutas están protegidas
5. **Logout permitido:** El usuario SÍ puede cerrar sesión desde la pantalla de cambio obligatorio si lo necesita

---

## 🐛 Troubleshooting

### "El email no llega"
- Verifica la configuración SMTP en `/configuracion`
- Prueba la conexión con el botón de test
- Revisa la carpeta de SPAM
- Gmail requiere contraseña de aplicación si tienes 2FA

### "La migración da error"
```sql
-- Si ya existen las columnas, ejecuta esto primero:
ALTER TABLE usuarios DROP COLUMN IF EXISTS estado;
ALTER TABLE usuarios DROP COLUMN IF EXISTS primer_login;
ALTER TABLE usuarios DROP COLUMN IF EXISTS ultimo_cambio_password;
-- Luego ejecuta migration_primer_login.sql
```

### "Loop infinito en cambio obligatorio"
- Verifica que `$_SESSION['cambio_password_obligatorio']` se limpie en `procesarCambioObligatorio()`
- Verifica que la BD esté actualizando `primer_login = 0` correctamente

---

## ✅ Checklist de Verificación

- [ ] Migración SQL ejecutada en la base de datos
- [ ] SMTP configurado y probado
- [ ] Crear nuevo usuario (sin campo de password manual)
- [ ] Recibir email con credenciales
- [ ] Login con password temporal redirige a cambio obligatorio
- [ ] Cambio de contraseña exitoso y acceso al dashboard
- [ ] Segundo login NO pide cambio (acceso directo)
- [ ] Usuarios existentes pueden loguearse normalmente

---

**Implementado:** 24 de febrero de 2026  
**Sistema:** CRM By Innovacode Tech v2.0.0
