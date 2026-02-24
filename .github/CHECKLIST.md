# Checklist - Configuración CI/CD

## ✅ Archivos Creados

- [x] `.gitignore` - Excluye archivos sensibles del repositorio
- [x] `.github/workflows/deploy.yml` - Workflow de GitHub Actions
- [x] `config/config.production.php.example` - Plantilla de configuración
- [x] `deploy-scripts/post-deploy.sh` - Script post-deployment
- [x] `.github/DEPLOYMENT.md` - Documentación detallada
- [x] `.github/COMMANDS.md` - Comandos útiles
- [x] `README.md` - Actualizado con sección de deployment

## 📋 Pasos Siguientes

### 1. Configurar GitHub Secrets ⏳
En tu repositorio de GitHub, ve a **Settings → Secrets and variables → Actions**

Crear estos 4 secrets:

- [ ] `SSH_PRIVATE_KEY` - La clave privada SSH (correspondiente a la pública proporcionada)
- [ ] `SSH_HOST` - Valor: `147.79.84.57`
- [ ] `SSH_PORT` - Valor: `65002`
- [ ] `SSH_USER` - Valor: `u329333801`

### 2. Configurar Hostinger ⏳

#### SSH Access
- [ ] Agregar clave pública SSH en **Advanced → SSH Access → Manage SSH Keys**
- [ ] Probar conexión: `ssh -p 65002 u329333801@147.79.84.57`

#### Base de Datos
- [ ] Crear base de datos: `u329333801_crm_bahari`
- [ ] Crear usuario: `u329333801_crmuser`
- [ ] Asignar permisos completos
- [ ] Importar esquema SQL completo

#### Configuración
- [ ] Crear directorio: `/home/u329333801/domains/crm.bahariaqua.com/public_html/`
- [ ] Copiar `config.production.php.example` a `config.php`
- [ ] Editar `config.php` con credenciales reales
- [ ] Configurar permisos: `chmod 755 logs uploads`

#### SSL/HTTPS
- [ ] Activar SSL en el panel de Hostinger
- [ ] Forzar HTTPS

### 3. Primer Deployment ⏳

#### Opción A: Automático (Recomendado)
```bash
git add .
git commit -m "Setup CI/CD deployment"
git push origin main
```
- [ ] Monitorear en **GitHub → Actions → Deploy to Hostinger**

#### Opción B: Manual
```bash
rsync -avz --delete \
  --exclude='.git' --exclude='logs/*.log' --exclude='config/config.php' \
  -e "ssh -p 65002" \
  ./ u329333801@147.79.84.57:/home/u329333801/domains/crm.bahariaqua.com/public_html/
```

### 4. Verificación ⏳
- [ ] Acceder a `https://crm.bahariaqua.com`
- [ ] Verificar página de login carga correctamente
- [ ] Probar inicio de sesión
- [ ] Revisar logs: `logs/error.log`
- [ ] Verificar workflow en GitHub Actions (debe estar en verde ✅)

## 🔍 Troubleshooting

Si algo falla:

### Error: "Permission denied (publickey)"
- Verifica que `SSH_PRIVATE_KEY` en GitHub Secrets sea correcto
- Asegúrate de que la clave pública esté en Hostinger

### Error: "Database connection failed"
- Revisa credenciales en `config/config.php` del servidor
- Verifica que el usuario tenga permisos sobre la base de datos

### Error: "rsync: failed to connect"
- Confirma puerto (65002) y host (147.79.84.57)
- Prueba conexión SSH manual

### Deployment exitoso pero sitio no funciona
- Verifica que `config/config.php` exista en el servidor
- Revisa logs de PHP: `tail -f logs/error.log`
- Confirma importación completa de base de datos

## 📚 Documentación

- **Deployment Completo:** `.github/DEPLOYMENT.md`
- **Comandos Útiles:** `.github/COMMANDS.md`
- **README Principal:** `README.md`

## 🎯 Próximos Pasos (Después del Setup)

- [ ] Configurar backups automáticos de base de datos
- [ ] Implementar monitoreo de uptime
- [ ] Configurar notificaciones de deployment (Slack/Email)
- [ ] Crear entornos staging/producción separados
- [ ] Implementar tests automáticos antes de deployment

## 💡 Tips

1. **Nunca hagas push directo de credenciales** - Usa `.gitignore` y secrets
2. **Prueba en local primero** - Asegúrate de que funcione antes de push
3. **Lee los logs** - GitHub Actions y logs del servidor son tu mejor amigo
4. **Haz backups regulares** - Especialmente antes de migraciones grandes
5. **Documenta cambios** - Usa commits descriptivos

---

**Estado Actual:** 🟡 Configuración completa - Listos para push a GitHub

**Último Update:** 24 de febrero de 2026
