<?php
class TransaccionController {
    private $conn;
    private $table_name = "transacciones"; // Asegúrate de que esta tabla existe en tu base de datos

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para crear una transacción
    public function crearTransaccion($categoria, $monto, $tipo) {
        try {
            $query = "INSERT INTO " . $this->table_name . " (categoria, monto, tipo, fecha) 
                      VALUES (:categoria, :monto, :tipo, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':monto', $monto);
            $stmt->bindParam(':tipo', $tipo);

            return $stmt->execute(); // Retorna true si la ejecución fue exitosa
        } catch (PDOException $e) {
            // Log de errores o manejo adicional
            error_log("Error en crearTransaccion: " . $e->getMessage());
            return false;
        }
    }

    // Método para obtener todas las transacciones
    public function obtenerTransacciones() {
        try {
            $query = "SELECT id, categoria, monto, tipo, fecha FROM " . $this->table_name . " ORDER BY fecha DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna un array asociativo con los resultados
        } catch (PDOException $e) {
            error_log("Error en obtenerTransacciones: " . $e->getMessage());
            return [];
        }
    }
}
