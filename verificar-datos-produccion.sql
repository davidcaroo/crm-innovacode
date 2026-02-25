/* =====================================================
   DIAGNÓSTICO Y CORRECCIÓN DE DATOS - PRODUCCIÓN
   phpMyAdmin / MySQL
   ===================================================== */

/* =====================================================
   1. VERIFICAR USUARIOS CON CAMPOS VACÍOS
   ===================================================== */
SELECT
    id,
    nombre,
    email,
    COALESCE(rol, 'NULL') AS rol,
    COALESCE(estado, 'NULL') AS estado
FROM usuarios;

/* =====================================================
   2. ACTUALIZAR USUARIOS CON ROL VACÍO
   ===================================================== */
UPDATE usuarios
SET rol = 'usuario'
WHERE rol IS NULL OR rol = '';

/* =====================================================
   3. ACTUALIZAR USUARIOS CON ESTADO VACÍO
   ===================================================== */
UPDATE usuarios
SET estado = 'activo'
WHERE estado IS NULL OR estado = '';

/* =====================================================
   4. VERIFICAR EMPRESAS CON ETAPA_VENTA VACÍA
   ===================================================== */
SELECT
    id,
    razon_social,
    COALESCE(etapa_venta, 'NULL') AS etapa_venta
FROM empresas
WHERE etapa_venta IS NULL OR etapa_venta = ''
LIMIT 10;

/* =====================================================
   5. ACTUALIZAR EMPRESAS CON ETAPA_VENTA VACÍA
   ===================================================== */
UPDATE empresas
SET etapa_venta = 'prospectado'
WHERE etapa_venta IS NULL OR etapa_venta = '';

/* =====================================================
   6. VERIFICACIÓN FINAL
   ===================================================== */
SELECT
    'Usuarios sin rol' AS problema,
    COUNT(*) AS cantidad
FROM usuarios
WHERE rol IS NULL OR rol = ''

UNION ALL

SELECT
    'Usuarios sin estado' AS problema,
    COUNT(*) AS cantidad
FROM usuarios
WHERE estado IS NULL OR estado = ''

UNION ALL

SELECT
    'Empresas sin etapa_venta' AS problema,
    COUNT(*) AS cantidad
FROM empresas
WHERE etapa_venta IS NULL OR etapa_venta = '';
