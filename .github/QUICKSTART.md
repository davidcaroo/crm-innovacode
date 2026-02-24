# 🚀 Quick Start - CI/CD Setup

## Configuración en 5 Minutos

### 1️⃣ Verificar Archivos
```bash
# Windows
.\verify-setup.bat

# Linux/Mac
chmod +x verify-setup.sh
./verify-setup.sh
```

### 2️⃣ Configurar GitHub Secrets

Ve a: **Settings → Secrets and variables → Actions**

Crea estos 4 secrets:

| Nombre | Valor |
|--------|-------|
| `SSH_PRIVATE_KEY` | [Tu clave privada SSH] |
| `SSH_HOST` | `147.79.84.57` |
| `SSH_PORT` | `65002` |
| `SSH_USER` | `u329333801` |

### 3️⃣ Configurar Hostinger

```bash
# Conectarse
ssh -p 65002 u329333801@147.79.84.57

# Crear estructura
cd /home/u329333801/domains/crm.bahariaqua.com/public_html/
mkdir -p logs uploads config

# Configurar
cp config/config.production.php.example config/config.php
nano config/config.php  # Editar credenciales

# Importar BD
mysql -u u329333801_crmuser -p u329333801_crm_bahari < esquema.sql
```

### 4️⃣ Primer Deployment

```bash
git add .
git commit -m "Setup CI/CD"
git push origin main
```

### 5️⃣ Verificar

✅ Ir a **Actions** en GitHub → Ver workflow  
✅ Abrir https://crm.bahariaqua.com  
✅ Probar login

---

## 📚 Documentación Completa

- **Guía Detallada:** `.github/DEPLOYMENT.md`
- **Comandos Útiles:** `.github/COMMANDS.md`
- **Checklist Completo:** `.github/CHECKLIST.md`

## 🆘 Ayuda Rápida

**Error de SSH?**
```bash
ssh-keyscan -p 65002 147.79.84.57 >> ~/.ssh/known_hosts
```

**Error de BD?**
```bash
mysql -u u329333801_crmuser -p u329333801_crm_bahari -e "SHOW TABLES"
```

**Ver Logs?**
```bash
ssh -p 65002 u329333801@147.79.84.57
tail -f /home/u329333801/domains/crm.bahariaqua.com/public_html/logs/error.log
```

---

**¿Listo?** Ejecuta `git push origin main` y observa la magia ✨
