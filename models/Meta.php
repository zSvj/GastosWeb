<?php
class Meta {
    private $conn;
    private $table = 'metas';

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para actualizar el ahorro de la meta
    public function actualizarAhorro($id, $ahorro_actual, $ahorro_objetivo) {
        $query = "UPDATE " . $this->table . " SET ahorro_actual = :ahorro_actual, ahorro_objetivo = :ahorro_objetivo WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":ahorro_actual", $ahorro_actual);
        $stmt->bindParam(":ahorro_objetivo", $ahorro_objetivo);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Método para eliminar una meta
    public function eliminarMeta($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Método para obtener los datos de la meta
    public function obtenerMeta($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
