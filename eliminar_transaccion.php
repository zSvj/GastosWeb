<?php
session_start();

require_once 'config/Database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Crear conexión a la base de datos
    $database = new Database();
    $db = $database->getConnection();

    // Preparar la consulta de eliminación
    $query = "DELETE FROM transacciones WHERE id = :id"; // Asegúrate de que la tabla es 'transacciones'
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Transacción eliminada con éxito!';
    } else {
        $_SESSION['mensaje'] = 'Hubo un error al eliminar la transacción.';
    }

    // Redirigir a la página de transacciones después de la eliminación
    header('Location: transacciones.php');
    exit();
} else {
    $_SESSION['mensaje'] = 'No se especificó un ID de transacción.';
    header('Location: transacciones.php');
    exit();
}
?>
