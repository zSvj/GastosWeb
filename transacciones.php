<?php
// Incluir la conexión a la base de datos
require_once "config/Database.php";

// Crear una nueva instancia de la base de datos
$database = new Database();
$db = $database->getConnection();

// Procesar el formulario de creación de una nueva transacción
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'];
    $categoria = $_POST['categoria'];
    $monto = $_POST['monto'];
    $descripcion = $_POST['descripcion'];

    $query = "INSERT INTO transacciones (tipo, categoria, monto, descripcion, fecha)
              VALUES (:tipo, :categoria, :monto, :descripcion, NOW())";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':categoria', $categoria);
    $stmt->bindParam(':monto', $monto);
    $stmt->bindParam(':descripcion', $descripcion);

    if ($stmt->execute()) {
        header("Location: transacciones.php");
        exit();
    } else {
        echo "Error al registrar la transacción.";
    }
}

// Consulta SQL para obtener las transacciones con el nombre de la categoría
$query = "SELECT transacciones.id, categorias.nombre AS categoria, transacciones.monto, transacciones.descripcion, transacciones.fecha, transacciones.tipo
          FROM transacciones
          INNER JOIN categorias ON transacciones.categoria = categorias.id
          ORDER BY transacciones.fecha DESC";

$stmt = $db->prepare($query);
$stmt->execute();
$transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consulta SQL para obtener las categorías disponibles
$queryCategorias = "SELECT id, nombre FROM categorias";
$stmtCategorias = $db->prepare($queryCategorias);
$stmtCategorias->execute();
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transacciones</title>

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
        <h1 class="text-center">Transacciones Registradas</h1>

        <!-- Formulario para agregar una nueva transacción -->
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="h5">Agregar Nueva Transacción</h2>
            </div>
            <div class="card-body">
                <form action="transacciones.php" method="POST">
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select id="tipo" name="tipo" class="form-select" required>
                            <option value="ingreso">Ingreso</option>
                            <option value="gasto">Gasto</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoría</label>
                        <select id="categoria" name="categoria" class="form-select" required>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= htmlspecialchars($categoria['id']) ?>"><?= htmlspecialchars($categoria['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto</label>
                        <input type="number" step="0.01" id="monto" name="monto" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar Transacción</button>
                </form>
            </div>
        </div>

        <!-- Tabla de Transacciones -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
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
                        <td><?= htmlspecialchars($transaccion['id']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($transaccion['tipo'])) ?></td>
                        <td><?= htmlspecialchars($transaccion['categoria']) ?></td>
                        <td>$<?= number_format($transaccion['monto'], 2) ?></td>
                        <td><?= htmlspecialchars($transaccion['descripcion']) ?></td>
                        <td><?= htmlspecialchars($transaccion['fecha']) ?></td>
                        <td>
                            <a href="editar_transaccion.php?id=<?= $transaccion['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                            <a href="eliminar_transaccion.php?id=<?= $transaccion['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de que desea eliminar esta transacción?');">Eliminar</a>
                        </td>
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
</body>
</html>
