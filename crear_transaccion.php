<?php
require_once "../config/Database.php";

$database = new Database();
$db = $database->getConnection();

if ($_POST) {
    $tipo = $_POST['tipo'];
    $monto = $_POST['monto'];
    $categoria_id = $_POST['categoria_id'];

    $query = "INSERT INTO transacciones (tipo, monto, categoria_id) VALUES (:tipo, :monto, :categoria_id)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':monto', $monto);
    $stmt->bindParam(':categoria_id', $categoria_id);

    if ($stmt->execute()) {
        echo "Transacción registrada con éxito.";
    } else {
        echo "Error al registrar la transacción.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Transacción</title>
</head>
<body>
    <h1>Registrar Nueva Transacción</h1>
    <form method="POST" action="">
        <label for="tipo">Tipo de Transacción:</label>
        <select id="tipo" name="tipo">
            <option value="ingreso">Ingreso</option>
            <option value="gasto">Gasto</option>
        </select>

        <label for="monto">Monto:</label>
        <input type="number" id="monto" name="monto" required>

        <label for="categoria_id">Categoría:</label>
        <input type="number" id="categoria_id" name="categoria_id" required>

        <button type="submit">Registrar</button>
    </form>
</body>
</html>
