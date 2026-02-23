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
        $sql  = "SELECT id, nombre, email, rol, creado_en FROM usuarios ORDER BY creado_en DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function obtener($id)
    {
        $sql  = "SELECT id, nombre, email, rol, creado_en FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function actualizar($id, $nombre, $email, $rol)
    {
        $sql  = "UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nombre, $email, $rol, $id]);
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
