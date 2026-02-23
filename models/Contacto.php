<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/BaseModel.php';

class Contacto extends BaseModel
{
    protected $table = 'contactos';
    protected $primaryKey = 'id';

    public function crear($data)
    {
        $sql = "INSERT INTO contactos (empresa_id, nombre, cargo, email, telefono) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['empresa_id'],
            $data['nombre'],
            $data['cargo'],
            $data['email'],
            $data['telefono']
        ]);
    }

    public function todosPorEmpresa($empresa_id)
    {
        $sql = "SELECT * FROM contactos WHERE empresa_id = ? ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$empresa_id]);
        return $stmt->fetchAll();
    }

    public function obtener($id)
    {
        $sql = "SELECT * FROM contactos WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function actualizar($id, $data)
    {
        $sql = "UPDATE contactos SET nombre=?, cargo=?, email=?, telefono=? WHERE id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['cargo'],
            $data['email'],
            $data['telefono'],
            $id
        ]);
    }

    public function eliminar($id)
    {
        $sql = "DELETE FROM contactos WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
