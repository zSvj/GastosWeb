<?php
// Incluir la conexión a la base de datos
require_once "config/Database.php"; // Ruta corregida

// Crear una nueva instancia de la base de datos
$database = new Database();
$db = $database->getConnection();

// Consulta SQL para obtener los reportes de las transacciones por categoría
$query = "SELECT categorias.nombre AS categoria, SUM(transacciones.monto) AS total
          FROM transacciones
          INNER JOIN categorias ON transacciones.categoria = categorias.id
          GROUP BY categorias.nombre";

// Preparar y ejecutar la consulta
$stmt = $db->prepare($query);
$stmt->execute();

// Obtener los resultados de la consulta
$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta SQL para obtener los reportes de las metas
$query_metas = "SELECT nombre, objetivo, ahorro_actual FROM metas";
$stmt_metas = $db->prepare($query_metas);
$stmt_metas->execute();
$metas = $stmt_metas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Transacciones y Metas</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                        <a class="nav-link" href="metas.php">Metas Financieras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transacciones.php">Transacciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="reportes.php">Reportes</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container mt-4">
        <h1 class="text-center">Reportes de Transacciones por Categoría</h1>

        <!-- Tabla de Reportes de Transacciones -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportes as $reporte): ?>
                    <tr>
                        <td><?= htmlspecialchars($reporte['categoria']) ?></td>
                        <td>$<?= number_format($reporte['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h1 class="text-center mt-5">Reportes de Metas Financieras</h1>

        <!-- Tabla de Reportes de Metas -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Objetivo</th>
                    <th>Ahorro Actual</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($metas as $meta): ?>
                    <tr>
                        <td><?= htmlspecialchars($meta['nombre']) ?></td>
                        <td>$<?= number_format($meta['objetivo'], 2) ?></td>
                        <td>$<?= number_format($meta['ahorro_actual'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-4">
        <p>&copy; 2024 Gestión de Finanzas Personales. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 Script -->
    <script>
        // SweetAlert de bienvenida
        Swal.fire({
            title: '¡Bienvenido!',
            text: 'Navega por los reportes de tus transacciones y metas.',
            icon: 'info',
            confirmButtonText: 'Aceptar'
        });
    </script>
</body>
</html>
