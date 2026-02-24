# GitHub Actions Workflow para CI/CD - Documentación

## Estructura del Workflow

El archivo `.github/workflows/deploy.yml` contiene el workflow completo de deployment.

### Trigger
```yaml
on:
  push:
    branches:
      - main
      - master
```
Se ejecuta automáticamente en cada push a las ramas `main` o `master`.

### Jobs

#### Job: deploy
Corre en: `ubuntu-latest`

**Steps:**

1. **Checkout code**
   - Usa: `actions/checkout@v3`
   - Descarga el código del repositorio

2. **Setup SSH**
   - Crea directorio `~/.ssh`
   - Guarda la clave privada desde secrets
   - Configura permisos `600` para la clave
   - Agrega el host a `known_hosts`

3. **Deploy via rsync**
   - Sincroniza archivos al servidor
   - Excluye archivos sensibles/innecesarios
   - Usa SSH en puerto personalizado (65002)
   - Flag `--delete` elimina archivos remotos que ya no existen localmente

4. **Post-deployment tasks**
   - Verifica/crea directorios necesarios
   - Configura permisos correctos
   - Ejecuta tareas de mantenimiento

5. **Notifications**
   - Notifica éxito/fallo del deployment

## Secrets Requeridos

El workflow necesita estos secrets configurados en GitHub:

| Secret | Descripción | Ejemplo |
|--------|-------------|---------|
| `SSH_PRIVATE_KEY` | Clave privada SSH RSA | `-----BEGIN OPENSSH PRIVATE KEY-----\n...` |
| `SSH_HOST` | IP del servidor Hostinger | `147.79.84.57` |
| `SSH_PORT` | Puerto SSH | `65002` |
| `SSH_USER` | Usuario SSH | `u329333801` |

## Archivos Excluidos del Sync

El rsync excluye estos patrones:
- `.git/` - Historial de Git
- `.github/` - Workflows y documentación
- `logs/*.log` - Logs del servidor
- `config/config.php` - Configuración de producción
- `.env` - Variables de entorno
- `node_modules/` - Dependencias Node
- `vendor/` - Dependencias PHP
- `old_files_backup/` - Backups locales
- `.DS_Store`, `Thumbs.db` - Archivos del sistema

## Monitoreo y Logs

### Ver Ejecución en Vivo
1. Ve a tu repositorio en GitHub
2. Click en **Actions** (tab superior)
3. Selecciona el workflow "Deploy to Hostinger"
4. Click en la ejecución más reciente
5. Expande los steps para ver logs detallados

### Logs del Servidor
```bash
# Conectar por SSH
ssh -p 65002 u329333801@147.79.84.57

# Ver logs de deployment
tail -f /home/u329333801/domains/crm.bahariaqua.com/public_html/logs/deployment.log

# Ver logs de errores PHP
tail -f /home/u329333801/domains/crm.bahariaqua.com/public_html/logs/error.log
```

## Troubleshooting

### Workflow Falla en "Setup SSH"
**Causa:** Clave SSH incorrecta o mal formateada
**Solución:**
```bash
# Regenerar par de claves
ssh-keygen -t rsa -b 4096 -C "deployment@crm.bahariaqua.com"

# Copiar clave privada completa (incluyendo headers)
cat ~/.ssh/id_rsa
# Agregar como SSH_PRIVATE_KEY en GitHub Secrets

# Copiar clave pública
cat ~/.ssh/id_rsa.pub
# Agregar en Hostinger SSH Access
```

### Workflow Falla en "Deploy via rsync"
**Causa:** Conexión SSH rechazada
**Solución:**
```bash
# Verificar conexión manual
ssh -p 65002 u329333801@147.79.84.57

# Si falla, verificar:
# 1. Clave pública en Hostinger
# 2. SSH_HOST, SSH_PORT, SSH_USER en GitHub Secrets
# 3. Estado del servidor SSH en Hostinger
```

### Deployment Exitoso pero Sitio no Funciona
**Causa:** Falta configuración o base de datos
**Checklist:**
- [ ] `config/config.php` existe en el servidor
- [ ] Credenciales de BD correctas en `config.php`
- [ ] Base de datos creada e importada
- [ ] Permisos de directorios correctos (`755`)
- [ ] SSL/HTTPS activo

### rsync Elimina Archivos Importantes
**Causa:** Flag `--delete` elimina archivos no presentes localmente
**Solución:**
- Asegúrate de que archivos importantes estén en `.gitignore` Y en lista de exclusión de rsync
- Para recuperar: restaurar desde backup o recrear manualmente

## Personalización del Workflow

### Agregar Tests Antes de Deployment
```yaml
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: PHP Syntax Check
        run: find . -name "*.php" -exec php -l {} \;
      - name: Run Tests
        run: php vendor/bin/phpunit

  deploy:
    needs: test  # Solo deployar si tests pasan
    runs-on: ubuntu-latest
    # ... resto del job
```

### Deployment Solo en Tags
```yaml
on:
  push:
    tags:
      - 'v*'  # Solo en tags v1.0.0, v2.0.0, etc.
```

### Notificaciones por Email
```yaml
  - name: Send notification
    if: always()
    uses: dawidd6/action-send-mail@v3
    with:
      server_address: smtp.gmail.com
      server_port: 465
      username: ${{secrets.EMAIL_USERNAME}}
      password: ${{secrets.EMAIL_PASSWORD}}
      subject: Deployment ${{ job.status }}
      body: Deployment to crm.bahariaqua.com ${{ job.status }}
      to: tu@email.com
      from: GitHub Actions
```

### Deployment Paralelo (Staging + Producción)
```yaml
jobs:
  deploy-staging:
    if: github.ref == 'refs/heads/develop'
    # ... deploy a staging

  deploy-production:
    if: github.ref == 'refs/heads/main'
    # ... deploy a producción
```

## Mejores Prácticas

✅ **Siempre prueba localmente antes de push**
✅ **Usa branches para features, merge a main solo código probado**
✅ **Monitorea los logs de GitHub Actions después de cada push**
✅ **Mantén backups regulares de la base de datos**
✅ **No hagas push de credenciales (usa .gitignore)**
✅ **Documenta cambios importantes en commits**
✅ **Revisa exclusiones de rsync periódicamente**
✅ **Configura notificaciones para fallos de deployment**

## Seguridad

🔒 **Clave SSH privada nunca debe estar en el código**
🔒 **Secrets de GitHub son encriptados y no se muestran en logs**
🔒 **Conexión SSH usa autenticación por clave, no password**
🔒 **rsync excluye archivos de configuración sensibles**
🔒 **Workflow solo se ejecuta desde repositorio autorizado**

## Comandos Útiles

```bash
# Ver status del último workflow
gh run list --workflow=deploy.yml --limit 1

# Ver logs del último workflow
gh run view --log

# Cancelar workflow en ejecución
gh run cancel <run-id>

# Reejecutar workflow fallido
gh run rerun <run-id>

# Trigger manual (requiere workflow_dispatch)
gh workflow run deploy.yml
```

## Referencias

- [GitHub Actions Docs](https://docs.github.com/en/actions)
- [rsync Manual](https://linux.die.net/man/1/rsync)
- [SSH Key Authentication](https://www.ssh.com/academy/ssh/public-key-authentication)
- [Hostinger SSH Guide](https://support.hostinger.com/en/articles/1583245-how-to-use-ssh)

---

**Última Actualización:** 24 de febrero de 2026
