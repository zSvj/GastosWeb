<?php
require_once "../config/Database.php";
require_once "../models/Meta.php";

$database = new Database();
$db = $database->getConnection();

$meta = new Meta($db);

if ($_POST) {
    $id = $_POST['id'];
    $ahorro_actual = $_POST['ahorro_actual'];
    $ahorro_meta = $_POST['ahorro_meta'];

    $meta->actualizarAhorro($id, $ahorro_actual);

    echo "<p>Meta actualizada con éxito.</p>";
}

$id = $_GET['id'];
$query = "SELECT * FROM metas WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $id);
$stmt->execute();
$meta_actual = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Meta</title>
</head>
<body>
    <h1>Editar Meta Financiera</h1>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= $meta_actual['id'] ?>">

        <label for="ahorro_actual">Ahorro Actual:</label>
        <input type="number" id="ahorro_actual" name="ahorro_actual" value="<?= $meta_actual['ahorro_actual'] ?>" required>

        <label for="ahorro_meta">Meta de Ahorro:</label>
        <input type="number" id="ahorro_meta" name="ahorro_meta" value="<?= $meta_actual['ahorro_meta'] ?>" required>

        <button type="submit">Actualizar</button>
    </form>
</body>
</html>
