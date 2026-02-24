<?php

require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../config/Database.php';

class Usuario extends BaseModel
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';

    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function crear($nombre, $email, $password, $rol = 'usuario', $primerLogin = 1)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, primer_login, estado) VALUES (?, ?, ?, ?, ?, 'activo')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nombre, $email, $hash, $rol, $primerLogin]);
    }

    /**
     * Genera una contraseña temporal aleatoria segura
     * @param int $longitud Longitud de la contraseña (por defecto 12)
     * @return string Contraseña generada
     */
    public static function generarPasswordTemporal($longitud = 12)
    {
        $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        $max = strlen($caracteres) - 1;

        for ($i = 0; $i < $longitud; $i++) {
            $password .= $caracteres[random_int(0, $max)];
        }

        return $password;
    }

    public function validarLogin($email, $password)
    {
        $usuario = $this->buscarPorEmail($email);
        if ($usuario && password_verify($password, $usuario->password)) {
            return $usuario;
        }
        return false;
    }

    public function todos()
    {
        $sql  = "SELECT u.id, u.nombre, u.email, u.rol, u.estado, u.creado_en, 
                        (SELECT COUNT(*) FROM empresas e WHERE e.usuario_id = u.id) as total_empresas
                 FROM usuarios u 
                 ORDER BY (u.rol = 'superadmin') DESC, u.nombre ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function obtener($id)
    {
        $sql  = "SELECT id, nombre, email, rol, estado, creado_en FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtiene un usuario incluyendo el campo password
     * Solo para validaciones internas (login, cambio de contraseña)
     */
    public function obtenerConPassword($id)
    {
        $sql  = "SELECT id, nombre, email, password, rol, estado, primer_login, creado_en FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function actualizar($id, $nombre, $email, $rol, $estado = 'activo')
    {
        $sql  = "UPDATE usuarios SET nombre = ?, email = ?, rol = ?, estado = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nombre, $email, $rol, $estado, $id]);
    }

    public function cambiarPassword($id, $nuevaPassword, $esPrimerCambio = false)
    {
        $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);

        if ($esPrimerCambio) {
            $sql = "UPDATE usuarios SET password = ?, primer_login = 0, ultimo_cambio_password = NOW() WHERE id = ?";
        } else {
            $sql = "UPDATE usuarios SET password = ?, ultimo_cambio_password = NOW() WHERE id = ?";
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$hash, $id]);
    }

    public function eliminar($id)
    {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /* ============================
       Recuperación de contraseña
       ============================ */

    /**
     * Guarda el token de recuperación (pre-hasheado) y su expiración para el usuario con ese email.
     * Retorna el id del usuario, o false si no existe.
     */
    public function setRecoveryToken($email, $tokenHashed, $expira = null)
    {
        $usuario = $this->buscarPorEmail($email);
        if (!$usuario) {
            return false;
        }
        // Usamos DATE_ADD(NOW(), ...) directo en MySQL para evitar
        // desfase entre timezone de PHP y MySQL (común en XAMPP)
        $sql  = "UPDATE usuarios SET recovery_token = ?, recovery_expira = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tokenHashed, $usuario->id]);
        return $usuario->id;
    }

    /**
     * Busca un usuario por token hasheado que no haya expirado.
     */
    public function findByRecoveryToken($tokenHashed)
    {
        $sql  = "SELECT * FROM usuarios 
                 WHERE recovery_token = ? AND recovery_expira > NOW() 
                 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$tokenHashed]);
        return $stmt->fetch();
    }

    /**
     * Limpia el token de recuperación una vez usado.
     */
    public function clearRecoveryToken($id)
    {
        $sql  = "UPDATE usuarios SET recovery_token = NULL, recovery_expira = NULL WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
