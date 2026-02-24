# Documentación de CI/CD - CRM Bahari Aqua

## 📚 Índice de Documentación

### 🚀 Para Empezar
- **[QUICKSTART.md](QUICKSTART.md)** - Configuración en 5 minutos
- **[CHECKLIST.md](CHECKLIST.md)** - Checklist completo de setup

### 📖 Guías Detalladas
- **[DEPLOYMENT.md](DEPLOYMENT.md)** - Guía completa de deployment
- **[WORKFLOW.md](WORKFLOW.md)** - Documentación del workflow de GitHub Actions

### 🛠️ Referencia Técnica
- **[COMMANDS.md](COMMANDS.md)** - Comandos útiles para Git, SSH, DB, etc.

## 🎯 Flujo de Trabajo

```
┌─────────────────┐
│  Desarrollo     │
│  Local          │
└────────┬────────┘
         │ git add / commit
         ▼
┌─────────────────┐
│  git push       │
│  origin main    │
└────────┬────────┘
         │ trigger
         ▼
┌─────────────────┐
│  GitHub Actions │
│  Workflow       │
└────────┬────────┘
         │ rsync over SSH
         ▼
┌─────────────────┐
│  Hostinger      │
│  Producción     │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ crm.bahariaqua  │
│    .com         │
└─────────────────┘
```

## ⚙️ Archivos de Configuración

### Repositorio Local
```
crm-php.com/
├── .github/
│   ├── workflows/
│   │   └── deploy.yml          ← Workflow de GitHub Actions
│   ├── QUICKSTART.md           ← Inicio rápido
│   ├── DEPLOYMENT.md           ← Guía detallada
│   ├── WORKFLOW.md             ← Documentación técnica
│   ├── COMMANDS.md             ← Comandos útiles
│   ├── CHECKLIST.md            ← Checklist de setup
│   └── README.md               ← Este archivo
├── config/
│   ├── config.php              ← Config local (en .gitignore)
│   └── config.production...    ← Plantilla para producción
├── deploy-scripts/
│   └── post-deploy.sh          ← Script post-deployment
├── .gitignore                  ← Archivos excluidos de git
├── verify-setup.sh             ← Script de verificación (Unix)
└── verify-setup.bat            ← Script de verificación (Windows)
```

### Servidor Hostinger
```
/home/u329333801/domains/crm.bahariaqua.com/public_html/
├── config/
│   └── config.php              ← Config de producción (NO synceado)
├── logs/
│   ├── error.log               ← Logs PHP (NO synceados)
│   └── deployment.log          ← Logs de deployment
├── uploads/                    ← Archivos subidos (persistentes)
└── [resto de archivos del proyecto]
```

## 🔐 Secrets de GitHub

Configure estos secrets en: **Settings → Secrets → Actions**

| Secret | Descripción |
|--------|-------------|
| `SSH_PRIVATE_KEY` | Clave privada SSH |
| `SSH_HOST` | `147.79.84.57` |
| `SSH_PORT` | `65002` |
| `SSH_USER` | `u329333801` |

## 🚦 Verificación Rápida

### Antes del Primer Push
```bash
# Windows
.\verify-setup.bat

# Linux/Mac
chmod +x verify-setup.sh
./verify-setup.sh
```

### Después del Deployment
```bash
# Ver logs del workflow en GitHub
# https://github.com/TU_USUARIO/TU_REPO/actions

# Verificar sitio
curl -I https://crm.bahariaqua.com

# Ver logs del servidor
ssh -p 65002 u329333801@147.79.84.57 \
  "tail -50 /home/u329333801/domains/crm.bahariaqua.com/public_html/logs/error.log"
```

## 📊 Monitoreo

### GitHub Actions
- **URL:** `https://github.com/[TU_USUARIO]/[TU_REPO]/actions`
- **Badge:** Agrega este badge a tu README.md:
  ```markdown
  ![Deploy](https://github.com/[TU_USUARIO]/[TU_REPO]/workflows/Deploy%20to%20Hostinger/badge.svg)
  ```

### Logs del Servidor
```bash
# Error logs PHP
tail -f logs/error.log

# Deployment logs
tail -f logs/deployment.log

# Apache logs
tail -f ~/public_html/error_log
```

## 🆘 Troubleshooting Rápido

| Problema | Solución |
|----------|----------|
| SSH connection failed | Verificar secrets, clave pública en Hostinger |
| rsync failed | Verificar permisos de directorios en servidor |
| Sitio no carga | Revisar `config.php`, importar BD, verificar SSL |
| Errores 500 | Ver `logs/error.log`, verificar sintaxis PHP |
| BD connection error | Verificar credenciales en `config.php` |

Consulta [DEPLOYMENT.md](DEPLOYMENT.md) para soluciones detalladas.

## 🔄 Workflow Típico

### Desarrollo de Feature
```bash
# 1. Crear branch de feature
git checkout -b feature/nueva-funcionalidad

# 2. Hacer cambios y commits
git add .
git commit -m "Implementar nueva funcionalidad"

# 3. Push a GitHub
git push origin feature/nueva-funcionalidad

# 4. Crear Pull Request en GitHub
# 5. Después de revisión, merge a main
# 6. Deployment automático se ejecuta
```

### Hotfix Urgente
```bash
# 1. Crear branch de hotfix
git checkout -b hotfix/correccion-critica

# 2. Hacer corrección
git add .
git commit -m "Fix: corregir bug crítico"

# 3. Push y merge rápido a main
git push origin hotfix/correccion-critica
# Merge a main vía GitHub
# Deployment automático se ejecuta
```

### Rollback
```bash
# Opción 1: Revertir último commit
git revert HEAD
git push origin main

# Opción 2: Restaurar desde backup (manual en servidor)
```

## 🔧 Mantenimiento

### Tareas Regulares
- [ ] **Diario:** Revisar logs de error
- [ ] **Semanal:** Backup de base de datos
- [ ] **Mensual:** Limpiar logs antiguos
- [ ] **Trimestral:** Revisar y actualizar dependencias
- [ ] **Semestral:** Rotar credenciales sensibles

### Comandos de Mantenimiento
```bash
# Backup de BD
ssh -p 65002 u329333801@147.79.84.57
mysqldump -u u329333801_crmuser -p u329333801_crm_bahari > backup.sql

# Limpiar logs antiguos
find logs/ -name "*.log" -mtime +30 -delete

# Ver espacio en disco
df -h
```

## 📞 Soporte y Recursos

### Documentación Oficial
- [GitHub Actions](https://docs.github.com/en/actions)
- [Hostinger Help](https://support.hostinger.com)
- [SSH Documentation](https://www.ssh.com/academy/ssh)

### Contacto
- **Repositorio:** [GitHub Issues](https://github.com/[TU_USUARIO]/[TU_REPO]/issues)
- **Email:** tu@email.com

## 📝 Changelog

### v2.0.0 - CI/CD Implementation (24/02/2026)
- ✅ GitHub Actions workflow configurado
- ✅ Deployment automático a Hostinger
- ✅ Scripts de post-deployment
- ✅ Documentación completa
- ✅ Scripts de verificación

---

**¿Necesitas ayuda?** Consulta [QUICKSTART.md](QUICKSTART.md) para comenzar o [DEPLOYMENT.md](DEPLOYMENT.md) para guía detallada.

**¿Listo para deployar?** 
```bash
git push origin main
```
