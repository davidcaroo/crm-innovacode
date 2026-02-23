<?php

/**
 * Clase BaseModel
 * Modelo base con funcionalidad común para todos los modelos
 * Implementa el patrón Active Record simplificado
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';

abstract class BaseModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    /**
     * Constructor
     */
    public function __construct()
    {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }

    /**
     * Obtener todos los registros de la tabla
     * 
     * @return array Array de objetos
     */
    public function all()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->db->query($query);
        return $stmt->fetchAll();
    }

    /**
     * Obtener un registro por ID
     * 
     * @param int $id ID del registro
     * @return object|false Objeto del registro o false si no existe
     */
    public function find($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE " . $this->primaryKey . " = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetchObject();
    }

    /**
     * Eliminar un registro por ID
     * 
     * @param int $id ID del registro a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE " . $this->primaryKey . " = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Contar todos los registros
     * 
     * @return int Número total de registros
     */
    public function count()
    {
        $query = "SELECT COUNT(*) as conteo FROM " . $this->table;
        $stmt = $this->db->query($query);
        return $stmt->fetchObject()->conteo;
    }

    /**
     * Ejecutar una consulta personalizada
     * 
     * @param string $query Consulta SQL
     * @param array $params Parámetros para prepared statement
     * @return PDOStatement Statement ejecutado
     */
    protected function query($query, $params = [])
    {
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Iniciar transacción
     */
    protected function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    /**
     * Confirmar transacción
     */
    protected function commit()
    {
        $this->db->commit();
    }

    /**
     * Revertir transacción
     */
    protected function rollback()
    {
        $this->db->rollBack();
    }

    /**
     * Validar que un valor no esté vacío
     * 
     * @param mixed $value Valor a validar
     * @param string $fieldName Nombre del campo (para el mensaje de error)
     * @return bool True si es válido
     * @throws Exception Si la validación falla
     */
    protected function validateRequired($value, $fieldName)
    {
        if (empty($value) && $value !== '0' && $value !== 0) {
            throw new Exception("El campo {$fieldName} es obligatorio.");
        }
        return true;
    }

    /**
     * Validar que un valor sea numérico
     * 
     * @param mixed $value Valor a validar
     * @param string $fieldName Nombre del campo
     * @return bool True si es válido
     * @throws Exception Si la validación falla
     */
    protected function validateNumeric($value, $fieldName)
    {
        if (!is_numeric($value)) {
            throw new Exception("El campo {$fieldName} debe ser numérico.");
        }
        return true;
    }

    /**
     * Sanitizar string para evitar XSS
     * 
     * @param string $value Valor a sanitizar
     * @return string Valor sanitizado
     */
    protected function sanitize($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
