<?php
session_start();

require_once 'config/Database.php';

// Crear conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Consulta para obtener las transacciones
$query = "SELECT * FROM transacciones";
$stmt = $db->prepare($query);
$stmt->execute();
$transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifica si hay un mensaje de éxito en la sesión
if (isset($_SESSION['mensaje'])) {
    echo "<script>
            Swal.fire({
                title: '¡Éxito!',
                text: '" . $_SESSION['mensaje'] . "',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
          </script>";

    // Elimina el mensaje de la sesión después de mostrarlo
    unset($_SESSION['mensaje']);
}

// Agregar nueva transacción
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoria = $_POST['categoria'];
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];

    // Inserta la nueva transacción en la base de datos
    $insertQuery = "INSERT INTO transacciones (categoria, monto, descripcion, fecha) VALUES (:categoria, :monto, :descripcion, :fecha)";
    $stmt = $db->prepare($insertQuery);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':monto', $monto);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':fecha', $fecha);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Transacción agregada con éxito';
        header("Location: transacciones.php"); // Redirige después de guardar
        exit();
    } else {
        $_SESSION['mensaje'] = 'Hubo un error al agregar la transacción';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Transacciones</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Barra de Navegación -->
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
                        <a class="nav-link active" href="transacciones.php">Transacciones</a>
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
        <h1>Lista de Transacciones</h1>

        <!-- Formulario para Agregar Transacción -->
        <div class="mb-4">
            <h3>Agregar Nueva Transacción</h3>
            <form action="transacciones.php" method="POST">
                <div class="mb-3">
                    <label for="categoria" class="form-label">Categoría</label>
                    <input type="text" class="form-control" id="categoria" name="categoria" required>
                </div>
                <div class="mb-3">
                    <label for="monto" class="form-label">Monto</label>
                    <input type="number" class="form-control" id="monto" name="monto" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                </div>
                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Transacción</button>
            </form>
        </div>

        <!-- Mostrar Transacciones -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th>Monto</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transacciones as $transaccion): ?>
                    <tr>
                        <td><?= htmlspecialchars($transaccion['categoria']) ?></td>
                        <td>$<?= number_format($transaccion['monto'], 2) ?></td>
                        <td><?= htmlspecialchars($transaccion['descripcion']) ?></td>
                        <td><?= htmlspecialchars($transaccion['fecha']) ?></td>
                        <td>
                            <a href="editar_transaccion.php?id=<?= $transaccion['id'] ?>" class="btn btn-warning">Editar</a>
                            <a href="eliminar_transaccion.php?id=<?= $transaccion['id'] ?>" class="btn btn-danger">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-4">
        <p>&copy; 2024 Finanzas Personales. Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 Script -->
    <script>
        function mostrarSweetAlert() {
            Swal.fire({
                title: '¡Bienvenido!',
                text: 'Navega por las metas, transacciones y reportes para gestionar tus finanzas.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
        }
    </script>
</body>
</html>
