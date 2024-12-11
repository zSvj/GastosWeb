<?php
require_once __DIR__ . '/../config/Database.php';

class Meta {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Obtener todas las metas
    public function obtenerMetas() {
        try {
            $query = "SELECT * FROM metas";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Manejo de errores
            echo "Error al obtener las metas: " . $e->getMessage();
            return [];
        }
    }

    // Actualizar ahorro actual de una meta específica
    public function actualizarMeta($id, $ahorro_actual) {
        try {
            $query = "UPDATE metas SET ahorro_actual = :ahorro_actual WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':ahorro_actual', $ahorro_actual, PDO::PARAM_INT);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // Manejo de errores
            echo "Error al actualizar la meta: " . $e->getMessage();
            return false;
        }
    }
}
?>
