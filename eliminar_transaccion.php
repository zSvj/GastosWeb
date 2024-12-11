<?php
require_once "config/Database.php";

// Verifica si se pasó el ID de la transacción
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $database = new Database();
    $db = $database->getConnection();

    // Consulta para eliminar la transacción
    $query = "DELETE FROM transacciones WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        echo "<script>
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'Transacción eliminada correctamente.',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(function() {
                    window.location.href = 'transacciones.php';
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    title: '¡Error!',
                    text: 'No se pudo eliminar la transacción.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
              </script>";
    }
} else {
    echo "ID no proporcionado.";
}
?>
