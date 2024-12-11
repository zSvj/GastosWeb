<?php
require_once "config/Database.php";

$database = new Database();
$db = $database->getConnection();

// Verifica si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica que el campo ahorro_objetivo exista antes de usarlo
    $ahorro_objetivo = $_POST['ahorro_objetivo'] ?? 0; // Si no se define, el valor será 0
    
    // Suponiendo que también se tiene algún ID de la meta que se está editando
    $id_meta = $_POST['id_meta'] ?? 0; // Asume que el ID de la meta se pasa con el formulario
    
    // Actualizar en la base de datos (por ejemplo)
    $query = "UPDATE metas SET ahorro_objetivo = :ahorro_objetivo WHERE id = :id_meta";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':ahorro_objetivo', $ahorro_objetivo);
    $stmt->bindParam(':id_meta', $id_meta);
    
    if ($stmt->execute()) {
        echo "Meta actualizada correctamente.";
    } else {
        echo "Error al actualizar la meta.";
    }
}

// Obtener las metas para mostrarlas en la página
$query = "SELECT * FROM metas";
$stmt = $db->prepare($query);
$stmt->execute();
$metas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Metas Financieras</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Finanzas Personales</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="metas.php">Metas Financieras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transacciones.php">Transacciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reportes.php">Reportes</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container mt-4">
        <h1 class="text-center">Metas Financieras</h1>
        
        <!-- Formulario para crear o editar metas -->
        <form action="metas.php" method="POST">
            <div class="mb-3">
                <label for="nombre_meta" class="form-label">Nombre de la Meta</label>
                <input type="text" name="nombre_meta" class="form-control" id="nombre_meta" required>
            </div>
            <div class="mb-3">
                <label for="ahorro_actual" class="form-label">Ahorro Actual</label>
                <input type="number" name="ahorro_actual" class="form-control" id="ahorro_actual" required>
            </div>
            <div class="mb-3">
                <label for="ahorro_objetivo" class="form-label">Ahorro Objetivo</label>
                <input type="number" name="ahorro_objetivo" class="form-control" id="ahorro_objetivo" value="0" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Meta</button>
        </form>

        <hr>

        <!-- Tabla de metas -->
        <h2>Lista de Metas</h2>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Meta</th>
                    <th>Ahorro Actual</th>
                    <th>Ahorro Objetivo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($metas as $meta): ?>
                    <tr>
                        <td><?= htmlspecialchars($meta['nombre']) ?></td>
                        <td>$<?= number_format($meta['ahorro_actual'], 2) ?></td>
                        <td>$<?= number_format($meta['ahorro_objetivo'] ?? 0, 2) ?></td> <!-- Asegúrate de que no sea null -->
                        <td>
                            <a href="editar_meta.php?id=<?= $meta['id'] ?>" class="btn btn-warning">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
