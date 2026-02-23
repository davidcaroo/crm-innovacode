<?php

/**
 * Clase Database
 * Patrón Singleton para manejar la conexión PDO a la base de datos
 * Implementa mejores prácticas de seguridad y manejo de errores
 */

class Database
{
    private static $instance = null;
    private $connection = null;

    /**
     * Constructor privado para implementar Singleton
     */
    private function __construct()
    {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_PERSISTENT => false,
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);

            // Establecer charset UTF-8
            $this->connection->exec("SET NAMES " . DB_CHARSET);
        } catch (PDOException $e) {
            if (DEBUG_MODE) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            } else {
                error_log("Database connection error: " . $e->getMessage());
                die("Error al conectar con la base de datos. Por favor, contacte al administrador.");
            }
        }
    }

    /**
     * Obtiene la instancia única de Database (Singleton)
     * 
     * @return Database Instancia única
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtiene la conexión PDO
     * 
     * @return PDO Objeto de conexión PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Prevenir clonación del objeto
     */
    private function __clone() {}

    /**
     * Prevenir deserialización del objeto
     */
    public function __wakeup()
    {
        throw new Exception("No se puede deserializar un singleton.");
    }

    /**
     * Cierra la conexión (se llama automáticamente al destruir el objeto)
     */
    public function __destruct()
    {
        $this->connection = null;
    }
}
