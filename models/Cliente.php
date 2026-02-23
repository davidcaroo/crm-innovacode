<?php

/**
 * Modelo Cliente
 * Maneja todas las operaciones relacionadas con clientes
 */

require_once __DIR__ . '/BaseModel.php';

class Cliente extends BaseModel
{
    protected $table = 'clientes';

    /**
     * Agregar un nuevo cliente
     * 
     * @param string $nombre Nombre del cliente
     * @param int $edad Edad del cliente
     * @param string $departamento Departamento del cliente
     * @return bool True si se agregó correctamente
     */
    public function agregar($nombre, $edad, $departamento)
    {
        // Validaciones
        $this->validateRequired($nombre, 'nombre');
        $this->validateRequired($edad, 'edad');
        $this->validateRequired($departamento, 'departamento');
        $this->validateNumeric($edad, 'edad');

        $fechaRegistro = date("Y-m-d");

        $query = "INSERT INTO {$this->table} (nombre, edad, departamento, fecha_registro) 
                  VALUES (?, ?, ?, ?)";

        $stmt = $this->query($query, [$nombre, $edad, $departamento, $fechaRegistro]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Actualizar un cliente existente
     * 
     * @param int $id ID del cliente
     * @param string $nombre Nombre del cliente
     * @param int $edad Edad del cliente
     * @param string $departamento Departamento del cliente
     * @return bool True si se actualizó correctamente
     */
    public function actualizar($id, $nombre, $edad, $departamento)
    {
        // Validaciones
        $this->validateRequired($id, 'id');
        $this->validateRequired($nombre, 'nombre');
        $this->validateRequired($edad, 'edad');
        $this->validateRequired($departamento, 'departamento');
        $this->validateNumeric($id, 'id');
        $this->validateNumeric($edad, 'edad');

        $query = "UPDATE {$this->table} 
                  SET nombre = ?, edad = ?, departamento = ? 
                  WHERE {$this->primaryKey} = ?";

        $stmt = $this->query($query, [$nombre, $edad, $departamento, $id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Buscar clientes por nombre
     * 
     * @param string $nombre Nombre o parte del nombre a buscar
     * @return array Array de clientes encontrados
     */
    public function buscarPorNombre($nombre)
    {
        $query = "SELECT * FROM {$this->table} WHERE nombre LIKE ? ORDER BY nombre ASC";
        $stmt = $this->query($query, ["%{$nombre}%"]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener clientes registrados en los últimos N días
     * 
     * @param int $dias Número de días
     * @return int Conteo de clientes
     */
    public function contarUltimosDias($dias = 30)
    {
        $fecha = date("Y-m-d", strtotime("-{$dias} day"));

        $query = "SELECT COUNT(*) AS conteo FROM {$this->table} 
                  WHERE fecha_registro >= ?";

        $stmt = $this->query($query, [$fecha]);
        return $stmt->fetchObject()->conteo;
    }

    /**
     * Obtener clientes registrados en el año actual
     * 
     * @return int Conteo de clientes
     */
    public function contarAnioActual()
    {
        $inicioAnio = date("Y-01-01");

        $query = "SELECT COUNT(*) AS conteo FROM {$this->table} 
                  WHERE fecha_registro >= ?";

        $stmt = $this->query($query, [$inicioAnio]);
        return $stmt->fetchObject()->conteo;
    }

    /**
     * Obtener clientes registrados antes del año actual
     * 
     * @return int Conteo de clientes
     */
    public function contarAniosAnteriores()
    {
        $inicioAnio = date("Y-01-01");

        $query = "SELECT COUNT(*) AS conteo FROM {$this->table} 
                  WHERE fecha_registro < ?";

        $stmt = $this->query($query, [$inicioAnio]);
        return $stmt->fetchObject()->conteo;
    }

    /**
     * Obtener conteo de clientes por departamento
     * 
     * @return array Array con departamento y conteo
     */
    public function contarPorDepartamento()
    {
        $query = "SELECT departamento, COUNT(*) AS conteo 
                  FROM {$this->table} 
                  GROUP BY departamento 
                  ORDER BY conteo DESC";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll();
    }

    /**
     * Obtener conteo de clientes por rango de edad
     * 
     * @param int $inicio Edad inicial del rango
     * @param int $fin Edad final del rango
     * @return int Conteo de clientes en ese rango
     */
    public function contarPorRangoEdad($inicio, $fin)
    {
        $query = "SELECT COUNT(*) AS conteo FROM {$this->table} 
                  WHERE edad >= ? AND edad <= ?";

        $stmt = $this->query($query, [$inicio, $fin]);
        return $stmt->fetchObject()->conteo;
    }

    /**
     * Obtener reporte de clientes por rangos de edad predefinidos
     * 
     * @return array Array con etiquetas y valores de rangos
     */
    public function reporteEdades()
    {
        $rangos = [
            [1, 10],
            [11, 20],
            [20, 40],
            [40, 80],
        ];

        $resultados = [];

        foreach ($rangos as $rango) {
            $inicio = $rango[0];
            $fin = $rango[1];
            $conteo = $this->contarPorRangoEdad($inicio, $fin);

            $dato = new stdClass();
            $dato->etiqueta = $inicio . " - " . $fin;
            $dato->valor = $conteo;

            $resultados[] = $dato;
        }

        return $resultados;
    }

    /**
     * Obtener todos los clientes ordenados por fecha de registro (más recientes primero)
     * 
     * @return array Array de clientes
     */
    public function obtenerTodosOrdenados()
    {
        $query = "SELECT * FROM {$this->table} 
                  ORDER BY fecha_registro DESC, nombre ASC";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll();
    }

    /**
     * Verificar si existe un cliente con el mismo nombre
     * 
     * @param string $nombre Nombre a verificar
     * @param int|null $idExcluir ID a excluir de la búsqueda (útil para actualizar)
     * @return bool True si existe
     */
    public function existeNombre($nombre, $idExcluir = null)
    {
        $query = "SELECT COUNT(*) AS conteo FROM {$this->table} WHERE nombre = ?";
        $params = [$nombre];

        if ($idExcluir !== null) {
            $query .= " AND {$this->primaryKey} != ?";
            $params[] = $idExcluir;
        }

        $stmt = $this->query($query, $params);
        return $stmt->fetchObject()->conteo > 0;
    }
}
