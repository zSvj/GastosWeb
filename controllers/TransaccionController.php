<?php
// Habilitar la visualización de errores para poder detectar problemas más fácilmente
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "config/Database.php";
require_once "models/Meta.php"; // Asegúrate de que la ruta sea correcta

// Conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Crear una instancia del objeto Meta
$meta = new Meta($db);

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se obtienen los datos enviados por el formulario
    $id = $_POST['id'];
    $ahorro_actual = $_POST['metas'];
    $ahorro_objetivo = $_POST['metas']; // Asegúrate de que este nombre coincida con la base de datos

    // Actualizar el ahorro actual usando el método de la clase Meta
    if ($meta->actualizarAhorro($id, $metas, $metas)) {
        // Si la actualización es exitosa, redirige a la página de metas
        echo "<script>
                Swal.fire({
                    title: 'Éxito!',
                    text: 'Meta actualizada correctamente.',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    window.location.href = 'metas.php'; // Redirige a la página de metas
                });
              </script>";
    } else {
        // Si ocurre un error, muestra una alerta de error
        echo "<script>
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo actualizar la meta.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
              </script>";
    }
}

// Obtener los datos de la meta a editar
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'No se proporcionó un ID válido.',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.href = 'metas.php'; // Redirige a la página de metas si no se proporciona un ID
            });
          </script>";
    exit;
}

$id = $_GET['id'];
$query = "SELECT * FROM metas WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $id);
$stmt->execute();
$meta_actual = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$meta_actual) {
    // Si no se encuentra la meta, muestra un mensaje de error
    echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'No se encontró la meta.',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                window.location.href = 'metas.php'; // Redirige a la página de metas si no existe la meta
            });
          </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Meta</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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
        <h1 class="text-center">Editar Meta Financiera</h1>
        
        <!-- Formulario para editar meta -->
        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= htmlspecialchars($meta_actual['id']) ?>">

            <div class="mb-3">
                <label for="nombre_meta" class="form-label">Nombre de la Meta</label>
                <input type="text" class="form-control" id="nombre_meta" value="<?= htmlspecialchars($meta_actual['nombre']) ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="ahorro_actual" class="form-label">Ahorro Actual</label>
                <input type="number" class="form-control" id="ahorro_actual" name="ahorro_actual" value="<?= htmlspecialchars($meta_actual['ahorro_actual']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="ahorro_objetivo" class="form-label">Meta de Ahorro</label>
                <input type="number" class="form-control" id="ahorro_objetivo" name="ahorro_objetivo" value="<?= htmlspecialchars($meta_actual['ahorro_objetivo']) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar Meta</button>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
