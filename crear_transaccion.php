<?php
session_start();
require_once 'config/Database.php';

// Crear conexión a la base de datos
$database = new Database();
$db = $database->getConnection();

// Obtener las categorías para el formulario de agregar transacción
$queryCategorias = "SELECT * FROM categorias";
$stmtCategorias = $db->prepare($queryCategorias);
$stmtCategorias->execute();
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

// Procesar formulario de agregar transacción
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categoria = $_POST['categoria'];
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];

    // Insertar nueva transacción
    $query = "INSERT INTO transacciones (categoria, monto, descripcion, fecha) VALUES (:categoria, :monto, :descripcion, :fecha)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':monto', $monto);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':fecha', $fecha);

    if ($stmt->execute()) {
        $_SESSION['mensaje'] = 'Transacción añadida con éxito!';
        header('Location: transacciones.php');
        exit();
    } else {
        $_SESSION['mensaje'] = 'Hubo un error al agregar la transacción.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Transacción</title>
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
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="metas.php">Metas Financieras</a></li>
                    <li class="nav-item"><a class="nav-link" href="transacciones.php">Transacciones</a></li>
                    <li class="nav-item"><a class="nav-link" href="reportes.php">Reportes</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>Crear Nueva Transacción</h1>
        
        <!-- Mostrar mensaje si existe -->
        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-info"><?= $_SESSION['mensaje']; unset($_SESSION['mensaje']); ?></div>
        <?php endif; ?>

        <!-- Formulario para agregar una nueva transacción -->
        <form method="POST">
            <div class="mb-3">
                <label for="categoria" class="form-label">Categoría</label>
                <select class="form-select" id="categoria" name="categoria" required>
                    <option value="">Seleccione una categoría</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria['id']; ?>"><?= $categoria['nombre']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="monto" class="form-label">Monto</label>
                <input type="number" class="form-control" id="monto" name="monto" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
            </div>
            <div class="mb-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" class="form-control" id="fecha" name="fecha" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar Transacción</button>
            <a href="transacciones.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 Script -->
    <script>
        function mostrarSweetAlert() {
            Swal.fire({
                title: '¡Transacción añadida con éxito!',
                text: 'Tu transacción ha sido guardada correctamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
        }
    </script>
</body>
</html>
