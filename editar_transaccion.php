<?php
session_start();

require_once 'config/Database.php';

$id = $_GET['id'];

// Crear conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Obtener la transacción por ID
$query = "SELECT * FROM transacciones WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$transaccion = $stmt->fetch(PDO::FETCH_ASSOC);

// Si no se encuentra la transacción, redirigir
if (!$transaccion) {
    header('Location: transacciones.php');
    exit();
}

// Procesar formulario de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria = $_POST['categoria'];
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];

    // Actualizar transacción
    $query = "UPDATE transacciones SET categoria = :categoria, monto = :monto, descripcion = :descripcion, fecha = :fecha WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':monto', $monto);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':fecha', $fecha);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Transacción actualizada con éxito!';
        header('Location: transacciones.php');
        exit();
    } else {
        $_SESSION['mensaje'] = 'Hubo un error al actualizar la transacción.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Transacción</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-4">
        <h1>Editar Transacción</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="categoria" class="form-label">Categoría</label>
                <input type="text" class="form-control" id="categoria" name="categoria" value="<?= htmlspecialchars($transaccion['categoria']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="monto" class="form-label">Monto</label>
                <input type="number" class="form-control" id="monto" name="monto" value="<?= $transaccion['monto'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" required><?= htmlspecialchars($transaccion['descripcion']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?= $transaccion['fecha'] ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Guardar Cambios</button>
            <a href="transacciones.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
