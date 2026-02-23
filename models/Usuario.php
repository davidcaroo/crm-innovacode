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

    public function crear($nombre, $email, $password, $rol = 'usuario')
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nombre, $email, $hash, $rol]);
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
                 ORDER BY u.creado_en DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function obtener($id)
    {
        $sql  = "SELECT id, nombre, email, rol, estado, creado_en FROM usuarios WHERE id = ?";
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

    public function cambiarPassword($id, $nuevaPassword)
    {
        $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        $sql  = "UPDATE usuarios SET password = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$hash, $id]);
    }

    public function eliminar($id)
    {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
