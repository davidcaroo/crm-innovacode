<?php
// models/Notificacion.php
require_once __DIR__ . '/BaseModel.php';

class Notificacion extends BaseModel
{
    protected $table = 'notificaciones';

    // --------------------------------------------------------
    // Crear notificación in-app
    // --------------------------------------------------------
    public static function crear(int $usuario_id, string $tipo, string $titulo, string $mensaje = '', string $url_accion = ''): bool
    {
        try {
            $db = self::getDB();
            $stmt = $db->prepare(
                "INSERT INTO notificaciones (usuario_id, tipo, titulo, mensaje, url_accion) VALUES (?, ?, ?, ?, ?)"
            );
            return $stmt->execute([$usuario_id, $tipo, $titulo, $mensaje, $url_accion]);
        } catch (Exception $e) {
            // Log silencioso para no interrumpir flujo de negocio
            error_log("Notificacion::crear error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Emitir un evento a todos los admins/superadmins usando preferencias configuradas.
     * Si las preferencias del rol permiten inapp, crea la notificación.
     */
    public static function emitir(string $evento, string $titulo, string $mensaje = '', string $url_accion = ''): void
    {
        try {
            $db = self::getDB();

            // Obtener todos los admins y superadmins
            $stmt = $db->prepare(
                "SELECT u.id, u.rol FROM usuarios u WHERE u.rol IN ('admin','superadmin')"
            );
            $stmt->execute();
            $admins = $stmt->fetchAll(PDO::FETCH_OBJ);

            foreach ($admins as $admin) {
                // Verificar preferencia del usuario (individual override)
                $pref = $db->prepare(
                    "SELECT canal_inapp FROM notificacion_preferencias WHERE usuario_id = ? AND evento = ? LIMIT 1"
                );
                $pref->execute([$admin->id, $evento]);
                $prefUsuario = $pref->fetch(PDO::FETCH_ASSOC);

                if ($prefUsuario !== false) {
                    // Preferencia individual encontrada
                    if ($prefUsuario['canal_inapp']) {
                        self::crear($admin->id, $evento, $titulo, $mensaje, $url_accion);
                    }
                } else {
                    // Usar preferencia del rol
                    $prefRol = $db->prepare(
                        "SELECT canal_inapp FROM notificacion_preferencias WHERE rol = ? AND usuario_id IS NULL AND evento = ? LIMIT 1"
                    );
                    $prefRol->execute([$admin->rol, $evento]);
                    $rolPref = $prefRol->fetch(PDO::FETCH_ASSOC);

                    if (!$rolPref || $rolPref['canal_inapp']) {
                        self::crear($admin->id, $evento, $titulo, $mensaje, $url_accion);
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Notificacion::emitir error: " . $e->getMessage());
        }
    }

    // --------------------------------------------------------
    // Lectura
    // --------------------------------------------------------
    public function getNoLeidas(int $usuario_id): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM notificaciones WHERE usuario_id = ? AND leida = 0 ORDER BY creado_en DESC LIMIT 20"
        );
        $stmt->execute([$usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function conteoNoLeidas(int $usuario_id): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM notificaciones WHERE usuario_id = ? AND leida = 0"
        );
        $stmt->execute([$usuario_id]);
        return (int)$stmt->fetchColumn();
    }

    public function getTodas(int $usuario_id, int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM notificaciones WHERE usuario_id = ? ORDER BY creado_en DESC LIMIT ?"
        );
        $stmt->execute([$usuario_id, $limit]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // --------------------------------------------------------
    // Marcar como leída
    // --------------------------------------------------------
    public function marcarLeida(int $id, int $usuario_id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE notificaciones SET leida = 1 WHERE id = ? AND usuario_id = ?"
        );
        return $stmt->execute([$id, $usuario_id]);
    }

    public function marcarTodasLeidas(int $usuario_id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE notificaciones SET leida = 1 WHERE usuario_id = ?"
        );
        return $stmt->execute([$usuario_id]);
    }

    // --------------------------------------------------------
    // Preferencias
    // --------------------------------------------------------
    public static function getPreferenciasUsuario(int $usuario_id): array
    {
        $db = self::getDB();
        $stmt = $db->prepare(
            "SELECT evento, canal_email, canal_inapp FROM notificacion_preferencias WHERE usuario_id = ?"
        );
        $stmt->execute([$usuario_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
        $prefs = [];
        foreach ($rows as $r) {
            $prefs[$r->evento] = $r;
        }
        return $prefs;
    }

    public static function getPreferenciasRol(string $rol): array
    {
        $db = self::getDB();
        $stmt = $db->prepare(
            "SELECT evento, canal_email, canal_inapp FROM notificacion_preferencias WHERE rol = ? AND usuario_id IS NULL"
        );
        $stmt->execute([$rol]);
        $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
        $prefs = [];
        foreach ($rows as $r) {
            $prefs[$r->evento] = $r;
        }
        return $prefs;
    }

    public static function setPreferencia(int $usuario_id, string $evento, bool $canal_email, bool $canal_inapp): void
    {
        $db = self::getDB();
        $stmt = $db->prepare(
            "INSERT INTO notificacion_preferencias (usuario_id, rol, evento, canal_email, canal_inapp)
             VALUES (?, NULL, ?, ?, ?)
             ON DUPLICATE KEY UPDATE canal_email = VALUES(canal_email), canal_inapp = VALUES(canal_inapp)"
        );
        $stmt->execute([$usuario_id, $evento, $canal_email ? 1 : 0, $canal_inapp ? 1 : 0]);
    }

    public static function setPreferenciasRol(string $rol, string $evento, bool $canal_email, bool $canal_inapp): void
    {
        $db = self::getDB();
        $stmt = $db->prepare(
            "INSERT INTO notificacion_preferencias (usuario_id, rol, evento, canal_email, canal_inapp)
             VALUES (NULL, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE canal_email = VALUES(canal_email), canal_inapp = VALUES(canal_inapp)"
        );
        $stmt->execute([$rol, $evento, $canal_email ? 1 : 0, $canal_inapp ? 1 : 0]);
    }

    // --------------------------------------------------------
    // Eventos disponibles (catálogo)
    // --------------------------------------------------------
    public static function eventosDisponibles(): array
    {
        return [
            'venta_ganada'      => ['label' => 'Venta registrada',     'icono' => 'mdi-cash-check'],
            'empresa_creada'    => ['label' => 'Nueva empresa creada', 'icono' => 'mdi-domain'],
            'cambio_etapa'      => ['label' => 'Cambio de etapa',      'icono' => 'mdi-arrow-right-circle'],
            'credito_aprobado'  => ['label' => 'Crédito aprobado',     'icono' => 'mdi-credit-card-check'],
        ];
    }
}
