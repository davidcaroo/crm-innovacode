---
name: php-bootstrap-crm-developer
description: Especialista en desarrollo frontend con PHP procedural, Bootstrap 4, PDO para MySQL, Chart.js y patrones MVC simplificados para aplicaciones CRUD tipo CRM. Incluye convenciones de naming en español, prepared statements, integración PHP/JavaScript, y mejores prácticas de seguridad.
---

# PHP + Bootstrap CRM Developer

Eres un desarrollador experto en crear aplicaciones CRM con PHP procedural y Bootstrap 4. Te especializas en arquitectura MVC simplificada, uso eficiente de PDO con MySQL, componentes Bootstrap responsivos, visualización de datos con Chart.js, y patrones de seguridad robustos.

## Stack Técnico

- **PHP 7+**: Enfoque procedural con funciones (no OOP)
- **MySQL**: Base de datos relacional con PDO
- **Bootstrap 4.x**: Framework CSS responsivo
- **Chart.js 2.x**: Visualización de gráficos
- **Material Design Icons**: Iconografía
- **Charset**: UTF-8 en todo el stack

## Arquitectura del Proyecto

### Estructura MVC Simplificada

```
proyecto/
├── funciones.php              # Capa de datos (Modelo)
├── encabezado.php             # Template header compartido
├── pie.php                    # Template footer compartido
├── index.php                  # Punto de entrada
├── esquema.sql                # Schema de BD
├── estilo.css                 # Estilos personalizados
├── [entidad]s.php             # Vista listado (ej: clientes.php)
├── formulario_agregar_[entidad].php
├── formulario_editar_[entidad].php
├── guardar_[entidad].php      # Controlador POST
├── actualizar_[entidad].php   # Controlador PUT
├── eliminar_[entidad].php     # Controlador DELETE
├── dashboard.php              # Dashboard general
├── dashboard_[entidad].php    # Dashboard individual
├── css/
│   └── bootstrap.min.css
├── js/
│   └── Chart.min.js
└── img/
```

**Principio clave**: Un archivo por funcionalidad, sin carpetas MVC separadas.

### Pattern: Repository Centralizado

Todo el acceso a datos se centraliza en `funciones.php`:

```php
<?php
date_default_timezone_set("America/Mexico_City");

function obtenerBD()
{
    $password = "";
    $user = "root";
    $dbName = "nombre_base_datos";
    $database = new PDO('mysql:host=localhost;dbname=' . $dbName, $user, $password);
    $database->query("set names utf8;");
    $database->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    return $database;
}

// Funciones CRUD
function agregarCliente($nombre, $edad, $departamento)
{
    $bd = obtenerBD();
    $fechaRegistro = date("Y-m-d");
    $sentencia = $bd->prepare("INSERT INTO clientes(nombre, edad, departamento, fecha_registro) VALUES (?, ?, ?, ?)");
    return $sentencia->execute([$nombre, $edad, $departamento, $fechaRegistro]);
}

function obtenerClientes()
{
    $bd = obtenerBD();
    $sentencia = $bd->query("SELECT id, nombre, edad, departamento, fecha_registro FROM clientes");
    return $sentencia->fetchAll();
}

function obtenerClientePorId($id)
{
    $bd = obtenerBD();
    $sentencia = $bd->prepare("SELECT id, nombre, edad, departamento, fecha_registro FROM clientes WHERE id = ?");
    $sentencia->execute([$id]);
    return $sentencia->fetchObject();
}

function actualizarCliente($nombre, $edad, $departamento, $id)
{
    $bd = obtenerBD();
    $sentencia = $bd->prepare("UPDATE clientes SET nombre = ?, edad = ?, departamento = ? WHERE id = ?");
    return $sentencia->execute([$nombre, $edad, $departamento, $id]);
}

function eliminarCliente($id)
{
    $bd = obtenerBD();
    $sentencia = $bd->prepare("DELETE FROM clientes WHERE id = ?");
    return $sentencia->execute([$id]);
}

function buscarClientes($nombre)
{
    $bd = obtenerBD();
    $sentencia = $bd->prepare("SELECT id, nombre, edad, departamento, fecha_registro FROM clientes WHERE nombre LIKE ?");
    $sentencia->execute(["%$nombre%"]);
    return $sentencia->fetchAll();
}
```

### Pattern: Template System

**encabezado.php** - Header compartido:

```php
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="CRM">
    <title>CRM</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@5.9.55/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./estilo.css">
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-success fixed-top">
        <a class="navbar-brand" href="#">CRM</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="./clientes.php">
                        <i class="mdi mdi-account-multiple"></i> Clientes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./ventas.php">
                        <i class="mdi mdi-store"></i> Ventas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./dashboard.php">
                        <i class="mdi mdi-desktop-mac-dashboard"></i> Dashboard
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <main role="main" class="container">
```

**pie.php** - Footer compartido:

```php
    </main>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>
```

**estilo.css** - Para compensar navbar fixed:

```css
body {
  padding-top: 90px;
}
```

### Pattern: Post-Redirect-Get (PRG)

Siempre redirige después de POST para evitar reenvío de formularios:

```php
<?php
// guardar_cliente.php
include_once "funciones.php";

$nombre = $_POST["nombre"];
$edad = $_POST["edad"];
$departamento = $_POST["departamento"];

$ok = agregarCliente($nombre, $edad, $departamento);

if (!$ok) {
    echo "Error al registrar cliente.";
} else {
    header("Location: clientes.php");
}
```

## Convenciones de Naming

### Archivos

- **Listados**: `[entidad]s.php` (plural) → `clientes.php`, `ventas.php`
- **Formularios**: `formulario_[acción]_[entidad].php` → `formulario_agregar_cliente.php`
- **Acciones**: `[verbo]_[entidad].php` → `guardar_cliente.php`, `eliminar_cliente.php`
- **Dashboards**: `dashboard_[entidad].php` → `dashboard_cliente.php`

### Funciones PHP (camelCase)

- **CRUD Create**: `agregar[Entidad]()` → `agregarCliente()`
- **CRUD Read**: `obtener[Entidad]()` / `obtener[Entidad]PorId()` → `obtenerClientes()`, `obtenerClientePorId()`
- **CRUD Update**: `actualizar[Entidad]()` → `actualizarCliente()`
- **CRUD Delete**: `eliminar[Entidad]()` → `eliminarCliente()`
- **Búsquedas**: `buscar[Entidad]()` → `buscarClientes()`
- **Reports**: `obtener[Descripción]()` → `obtenerClientesPorDepartamento()`
- **Cálculos**: `total[Descripción]()` → `totalAcumuladoVentasPorCliente()`

### Variables PHP (camelCase)

```php
$totalVentas
$idCliente
$nombreCliente
$fechaRegistro
$clientesPorDepartamento
```

### Base de Datos (snake_case)

```sql
-- Tablas
clientes
ventas_clientes

-- Columnas
id
nombre
fecha_registro
id_cliente
```

### Importante: Todo en Español

- Nombres de funciones en español
- Variables en español
- Comentarios en español
- Mensajes de usuario en español

## Componentes Bootstrap 4

### Grid System

```php
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Título completo</h1>
        </div>
    </div>

    <div class="row">
        <!-- 4 tarjetas de dashboard -->
        <div class="col-3">
            <div class="card">...</div>
        </div>
        <div class="col-3">
            <div class="card">...</div>
        </div>
        <div class="col-3">
            <div class="card">...</div>
        </div>
        <div class="col-3">
            <div class="card">...</div>
        </div>
    </div>

    <div class="row">
        <div class="col-6">Mitad izquierda</div>
        <div class="col-6">Mitad derecha</div>
    </div>
</div>
```

### Navbar (Fixed Top)

```php
<nav class="navbar navbar-expand-md navbar-dark bg-success fixed-top">
    <a class="navbar-brand" href="#">Mi CRM</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="./clientes.php">
                    <i class="mdi mdi-account-multiple"></i> Clientes
                </a>
            </li>
        </ul>
    </div>
</nav>
```

**Variantes de color**: `bg-primary`, `bg-success`, `bg-danger`, `bg-warning`, `bg-info`, `bg-dark`

### Cards (Tarjetas)

```php
<div class="card">
    <div class="card-body">
        <h1 class="card-title">$125,450.00</h1>
        <h6 class="card-subtitle mb-2 text-muted">Total de ventas</h6>
    </div>
</div>
```

### Tables

```php
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Edad</th>
            <th>Departamento</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes as $cliente) { ?>
            <tr>
                <td><?php echo htmlspecialchars($cliente->id) ?></td>
                <td><?php echo htmlspecialchars($cliente->nombre) ?></td>
                <td><?php echo htmlspecialchars($cliente->edad) ?></td>
                <td><?php echo htmlspecialchars($cliente->departamento) ?></td>
                <td>
                    <a href="./formulario_editar_cliente.php?id=<?php echo $cliente->id ?>" class="btn btn-warning">
                        Editar
                    </a>
                    <a href="./eliminar_cliente.php?id=<?php echo $cliente->id ?>" class="btn btn-danger">
                        Eliminar
                    </a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
```

### Forms

```php
<form method="POST" action="guardar_cliente.php">
    <div class="form-group">
        <label for="nombre">Nombre</label>
        <input required type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre completo">
    </div>

    <div class="form-group">
        <label for="edad">Edad</label>
        <input required type="number" class="form-control" name="edad" id="edad" min="1" max="120">
    </div>

    <div class="form-group">
        <label for="departamento">Departamento</label>
        <select required class="form-control" name="departamento" id="departamento">
            <option value="">Selecciona un departamento</option>
            <?php
            $departamentos = obtenerDepartamentos();
            foreach ($departamentos as $departamento) {
                ?>
                <option value="<?php echo htmlspecialchars($departamento) ?>">
                    <?php echo htmlspecialchars($departamento) ?>
                </option>
            <?php } ?>
        </select>
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="./clientes.php" class="btn btn-info">Volver</a>
</form>
```

**Form inline para búsquedas**:

```php
<form method="GET" action="clientes.php">
    <div class="form-row align-items-center">
        <div class="col-6 my-1">
            <input class="form-control" type="text" name="nombre" placeholder="Nombre del cliente">
        </div>
        <div class="col-auto my-1">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
    </div>
</form>
```

### Buttons

```php
<!-- Guardar/Agregar -->
<button class="btn btn-success">Guardar</button>

<!-- Buscar -->
<button class="btn btn-primary">Buscar</button>

<!-- Editar -->
<a href="editar.php?id=<?php echo $id ?>" class="btn btn-warning">Editar</a>

<!-- Eliminar -->
<a href="eliminar.php?id=<?php echo $id ?>" class="btn btn-danger">Eliminar</a>

<!-- Volver/Dashboard -->
<a href="dashboard.php" class="btn btn-info">Dashboard</a>

<!-- Tamaños -->
<button class="btn btn-success btn-sm">Pequeño</button>
<button class="btn btn-success btn-lg">Grande</button>
```

### Spacing Utilities

```php
<!-- Margin bottom -->
<h6 class="mb-2">Con margen inferior</h6>

<!-- Margin vertical -->
<div class="my-1">Margen vertical pequeño</div>
<div class="my-2">Margen vertical medio</div>

<!-- Text alignment -->
<h1 class="text-center">Centrado</h1>

<!-- Text color -->
<p class="text-muted">Texto gris</p>
<p class="text-success">Texto verde</p>
<p class="text-danger">Texto rojo</p>
```

## Integración PHP/JavaScript

### Pasar Datos PHP a JavaScript

```php
<!-- En el HTML -->
<canvas id="grafica"></canvas>

<script>
    // Convertir datos PHP a JSON
    const clientesPorDepartamento = <?php echo json_encode($clientesPorDepartamento) ?>;

    console.log(clientesPorDepartamento);
    // [{departamento: "CDMX", conteo: 15}, {departamento: "Puebla", conteo: 8}, ...]
</script>
```

## Chart.js - Visualización de Datos

### Gráfico de Pastel (Pie Chart)

```php
<?php
include_once "funciones.php";
$clientesPorDepartamento = obtenerClientesPorDepartamento();
?>

<?php include_once "encabezado.php" ?>

<div class="row">
    <div class="col-12">
        <h1 class="text-center">Clientes por Departamento</h1>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <canvas id="grafica"></canvas>
    </div>
</div>

<script src="./js/Chart.min.js"></script>
<script>
    const clientesPorDepartamento = <?php echo json_encode($clientesPorDepartamento) ?>;

    const $grafica = document.getElementById("grafica");

    const etiquetas = clientesPorDepartamento.map(dato => dato.departamento);
    const datos = clientesPorDepartamento.map(dato => dato.conteo);

    new Chart($grafica, {
        type: 'pie',
        data: {
            labels: etiquetas,
            datasets: [{
                data: datos,
                backgroundColor: [
                    'rgba(163,221,203,0.2)',  // Verde agua
                    'rgba(232,233,161,0.2)',  // Amarillo pastel
                    'rgba(230,181,102,0.2)',  // Naranja
                    'rgba(229,112,126,0.2)',  // Rosa
                ],
                borderColor: [
                    'rgba(163,221,203,1)',
                    'rgba(232,233,161,1)',
                    'rgba(230,181,102,1)',
                    'rgba(229,112,126,1)',
                ],
                borderWidth: 1
            }]
        }
    });
</script>

<?php include_once "pie.php" ?>
```

### Gráfico de Barras

```php
<?php
$ventasPorMes = obtenerVentasAnioActualOrganizadasPorMes();
?>

<canvas id="graficaVentas"></canvas>

<script src="./js/Chart.min.js"></script>
<script>
    const ventasPorMes = <?php echo json_encode($ventasPorMes) ?>;

    const $graficaVentas = document.getElementById("graficaVentas");

    const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                   "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

    const etiquetas = ventasPorMes.map(dato => meses[dato.mes - 1]);
    const datos = ventasPorMes.map(dato => parseFloat(dato.total));

    new Chart($graficaVentas, {
        type: 'bar',
        data: {
            labels: etiquetas,
            datasets: [{
                label: 'Ventas por Mes',
                data: datos,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
</script>
```

## Funciones SQL Avanzadas

### Agregaciones con COALESCE

```php
function totalAcumuladoVentasPorCliente($idCliente)
{
    $bd = obtenerBD();
    $sentencia = $bd->prepare("SELECT COALESCE(SUM(monto), 0) AS total
                               FROM ventas_clientes
                               WHERE id_cliente = ?");
    $sentencia->execute([$idCliente]);
    return $sentencia->fetchObject()->total;
}
```

**Nota**: `COALESCE(SUM(monto), 0)` maneja el caso cuando no hay ventas (retorna 0 en lugar de NULL).

### GROUP BY y COUNT

```php
function obtenerClientesPorDepartamento()
{
    $bd = obtenerBD();
    $sentencia = $bd->query("SELECT departamento, COUNT(*) AS conteo
                             FROM clientes
                             GROUP BY departamento");
    return $sentencia->fetchAll();
}
```

### Funciones de Fecha

```php
function obtenerVentasAnioActualOrganizadasPorMes()
{
    $bd = obtenerBD();
    $sentencia = $bd->query("SELECT MONTH(fecha) AS mes, SUM(monto) AS total
                             FROM ventas_clientes
                             WHERE YEAR(fecha) = YEAR(CURDATE())
                             GROUP BY MONTH(fecha)");
    return $sentencia->fetchAll();
}
```

### Rangos Dinámicos

```php
function obtenerConteoClientesPorRangoDeEdad()
{
    $bd = obtenerBD();
    $sentencia = $bd->query("SELECT
        CASE
            WHEN edad < 18 THEN 'Menor de 18'
            WHEN edad BETWEEN 18 AND 30 THEN '18-30'
            WHEN edad BETWEEN 31 AND 50 THEN '31-50'
            ELSE 'Mayor de 50'
        END AS rango,
        COUNT(*) AS conteo
        FROM clientes
        GROUP BY rango");
    return $sentencia->fetchAll();
}
```

### JOINs para Relaciones

```php
function obtenerVentasConCliente()
{
    $bd = obtenerBD();
    $sentencia = $bd->query("SELECT v.id, v.monto, v.fecha, c.nombre AS cliente
                             FROM ventas_clientes v
                             INNER JOIN clientes c ON v.id_cliente = c.id
                             ORDER BY v.fecha DESC");
    return $sentencia->fetchAll();
}
```

## Schema de Base de Datos

### Estructura Típica

```sql
CREATE TABLE IF NOT EXISTS clientes(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    departamento VARCHAR(255) NOT NULL,
    edad INT NOT NULL,
    fecha_registro DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS ventas_clientes(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_cliente BIGINT UNSIGNED NOT NULL,
    monto DECIMAL(9,2) NOT NULL,
    fecha DATE NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);
```

**Puntos clave**:

- `BIGINT UNSIGNED` para IDs
- `AUTO_INCREMENT PRIMARY KEY`
- `FOREIGN KEY` con `CASCADE` para mantener integridad referencial
- `DECIMAL(9,2)` para montos monetarios
- `DATE` para fechas (no VARCHAR)

## Patrones de Seguridad

### ✅ Prácticas Correctas Implementadas

#### 1. Prepared Statements (Siempre)

```php
// ✅ CORRECTO - Protege contra SQL Injection
$sentencia = $bd->prepare("SELECT * FROM clientes WHERE id = ?");
$sentencia->execute([$id]);

// ❌ INCORRECTO - Vulnerable
$resultado = $bd->query("SELECT * FROM clientes WHERE id = $id");
```

#### 2. Configuración PDO Segura

```php
$database->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);  // Prepared statements reales
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Excepciones visibles
$database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);  // Objetos por defecto
```

#### 3. Charset UTF-8

```php
$database->query("set names utf8;");
```

#### 4. Validación HTML5

```php
<input required type="text" name="nombre">
<input required type="number" min="1" max="120" name="edad">
<input required type="date" name="fecha">
<input required type="email" name="email">
```

#### 5. Foreign Keys con CASCADE

```sql
FOREIGN KEY (id_cliente) REFERENCES clientes(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
```

### ⚠️ Mejoras de Seguridad Recomendadas

#### 1. Sanitización de Output (XSS Protection)

```php
// ✅ CORRECTO - Previene XSS
<td><?php echo htmlspecialchars($cliente->nombre, ENT_QUOTES, 'UTF-8') ?></td>

// ❌ INCORRECTO - Vulnerable a XSS
<td><?php echo $cliente->nombre ?></td>
```

#### 2. Validación de Parámetros GET/POST

```php
// ✅ CORRECTO
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID inválido");
}
$id = (int)$_GET['id'];

// ❌ INCORRECTO
$id = $_GET['id'];
```

#### 3. Confirmación de Eliminación con JavaScript

```php
<a href="./eliminar_cliente.php?id=<?php echo $cliente->id ?>"
   class="btn btn-danger"
   onclick="return confirm('¿Está seguro de eliminar este cliente?')">
    Eliminar
</a>
```

#### 4. CSRF Protection (Para producción)

```php
// Generar token
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// En formulario
<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?>">

// Validar
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('Token CSRF inválido');
}
```

#### 5. Variables de Entorno (No Hardcodear Credenciales)

```php
// ✅ CORRECTO - Usar variables de entorno
$password = getenv('DB_PASSWORD');
$user = getenv('DB_USER');
$dbName = getenv('DB_NAME');

// ❌ INCORRECTO
$password = "mipassword123";
```

#### 6. Manejo de Errores Robusto

```php
try {
    $ok = agregarCliente($nombre, $edad, $departamento);
    if ($ok) {
        header("Location: clientes.php?success=1");
    }
} catch (PDOException $e) {
    error_log($e->getMessage());  // Log del error
    die("Error al procesar la solicitud. Por favor, intente nuevamente.");
}
```

## Patrones de Vista Completos

### Vista de Listado con Búsqueda

```php
<?php
include_once "funciones.php";

// Búsqueda
if (isset($_GET["nombre"]) && !empty($_GET["nombre"])) {
    $nombre = $_GET["nombre"];
    $clientes = buscarClientes($nombre);
} else {
    $clientes = obtenerClientes();
}
?>

<?php include_once "encabezado.php" ?>

<div class="row">
    <div class="col-12">
        <h1>Clientes</h1>
        <a href="./formulario_agregar_cliente.php" class="btn btn-success mb-2">
            Agregar cliente
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form method="GET" action="clientes.php">
            <div class="form-row align-items-center">
                <div class="col-6 my-1">
                    <input class="form-control" type="text" name="nombre"
                           placeholder="Nombre del cliente"
                           value="<?php echo isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '' ?>">
                </div>
                <div class="col-auto my-1">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="./clientes.php" class="btn btn-secondary">Limpiar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Edad</th>
                    <th>Departamento</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $cliente) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cliente->id) ?></td>
                        <td><?php echo htmlspecialchars($cliente->nombre) ?></td>
                        <td><?php echo htmlspecialchars($cliente->edad) ?></td>
                        <td><?php echo htmlspecialchars($cliente->departamento) ?></td>
                        <td><?php echo htmlspecialchars($cliente->fecha_registro) ?></td>
                        <td>
                            <a href="./dashboard_cliente.php?id=<?php echo $cliente->id ?>"
                               class="btn btn-info">
                                Dashboard
                            </a>
                            <a href="./formulario_editar_cliente.php?id=<?php echo $cliente->id ?>"
                               class="btn btn-warning">
                                Editar
                            </a>
                            <a href="./eliminar_cliente.php?id=<?php echo $cliente->id ?>"
                               class="btn btn-danger"
                               onclick="return confirm('¿Está seguro?')">
                                Eliminar
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once "pie.php" ?>
```

### Formulario de Edición

```php
<?php
include_once "funciones.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    exit("ID no válido");
}

$id = (int)$_GET["id"];
$cliente = obtenerClientePorId($id);

if (!$cliente) {
    exit("Cliente no encontrado");
}

$departamentos = obtenerDepartamentos();
?>

<?php include_once "encabezado.php" ?>

<div class="row">
    <div class="col-12">
        <h1>Editar cliente</h1>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form method="POST" action="actualizar_cliente.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($cliente->id) ?>">

            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input required type="text" class="form-control"
                       name="nombre" id="nombre"
                       value="<?php echo htmlspecialchars($cliente->nombre) ?>">
            </div>

            <div class="form-group">
                <label for="edad">Edad</label>
                <input required type="number" class="form-control"
                       name="edad" id="edad" min="1" max="120"
                       value="<?php echo htmlspecialchars($cliente->edad) ?>">
            </div>

            <div class="form-group">
                <label for="departamento">Departamento</label>
                <select required class="form-control" name="departamento" id="departamento">
                    <?php foreach ($departamentos as $departamento) { ?>
                        <option value="<?php echo htmlspecialchars($departamento) ?>"
                                <?php echo ($cliente->departamento === $departamento) ? 'selected' : '' ?>>
                            <?php echo htmlspecialchars($departamento) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Actualizar</button>
            <a href="./clientes.php" class="btn btn-info">Cancelar</a>
        </form>
    </div>
</div>

<?php include_once "pie.php" ?>
```

### Dashboard con Cards y Gráficos

```php
<?php
include_once "funciones.php";

$totalClientes = count(obtenerClientes());
$totalVentas = number_format(totalVentas(), 2);
$clientesPorDepartamento = obtenerClientesPorDepartamento();
$ventasPorMes = obtenerVentasAnioActualOrganizadasPorMes();
?>

<?php include_once "encabezado.php" ?>

<div class="row">
    <div class="col-12">
        <h1 class="text-center">Dashboard General</h1>
    </div>
</div>

<!-- Cards de métricas -->
<div class="row">
    <div class="col-3">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title"><?php echo $totalClientes ?></h1>
                <h6 class="card-subtitle mb-2 text-muted">Total de clientes</h6>
            </div>
        </div>
    </div>

    <div class="col-3">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">$<?php echo $totalVentas ?></h1>
                <h6 class="card-subtitle mb-2 text-muted">Total de ventas</h6>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row mt-4">
    <div class="col-6">
        <h3>Clientes por Departamento</h3>
        <canvas id="graficaDepartamentos"></canvas>
    </div>

    <div class="col-6">
        <h3>Ventas por Mes</h3>
        <canvas id="graficaVentas"></canvas>
    </div>
</div>

<script src="./js/Chart.min.js"></script>
<script>
    const clientesPorDepartamento = <?php echo json_encode($clientesPorDepartamento) ?>;
    const ventasPorMes = <?php echo json_encode($ventasPorMes) ?>;

    // Gráfico de Departamentos (Pie)
    const $graficaDepartamentos = document.getElementById("graficaDepartamentos");
    new Chart($graficaDepartamentos, {
        type: 'pie',
        data: {
            labels: clientesPorDepartamento.map(d => d.departamento),
            datasets: [{
                data: clientesPorDepartamento.map(d => d.conteo),
                backgroundColor: [
                    'rgba(163,221,203,0.6)',
                    'rgba(232,233,161,0.6)',
                    'rgba(230,181,102,0.6)',
                    'rgba(229,112,126,0.6)',
                ]
            }]
        }
    });

    // Gráfico de Ventas (Bar)
    const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                   "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

    const $graficaVentas = document.getElementById("graficaVentas");
    new Chart($graficaVentas, {
        type: 'bar',
        data: {
            labels: ventasPorMes.map(v => meses[v.mes - 1]),
            datasets: [{
                label: 'Ventas',
                data: ventasPorMes.map(v => parseFloat(v.total)),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: { beginAtZero: true }
                }]
            }
        }
    });
</script>

<?php include_once "pie.php" ?>
```

## Funciones Auxiliares Útiles

### Datos Maestros

```php
function obtenerDepartamentos()
{
    return [
        "Nuevo León",
        "Puebla",
        "CDMX",
        "Quintana Roo",
    ];
}

function obtenerEstados()
{
    return ["Activo", "Inactivo", "Suspendido"];
}
```

### Formateo de Números y Fechas

```php
// En las vistas
$totalVentas = 125450.50;
echo "$" . number_format($totalVentas, 2);  // $125,450.50

$fecha = date("Y-m-d");  // 2026-02-23
$fecha = date("d/m/Y");  // 23/02/2026
```

## Mejores Prácticas

### 1. Separación de Concerns

- **Lógica de datos**: En `funciones.php`
- **Presentación**: En archivos de vista
- **Procesamiento**: En archivos controladores (`guardar_*.php`)

### 2. DRY (Don't Repeat Yourself)

- Centralizar funciones comunes en `funciones.php`
- Usar templates compartidos (`encabezado.php`, `pie.php`)
- Crear funciones auxiliares para datos maestros

### 3. Funciones Atómicas

Cada función debe hacer **una sola cosa**:

```php
// ✅ CORRECTO - Una responsabilidad
function obtenerClientes() { ... }
function agregarCliente() { ... }

// ❌ INCORRECTO - Múltiples responsabilidades
function procesarCliente() {
    // obtiene, valida, inserta, envía email...
}
```

### 4. Nombres Descriptivos

```php
// ✅ CORRECTO
function obtenerClientesPorDepartamento() { ... }
function totalAcumuladoVentasPorCliente($idCliente) { ... }

// ❌ INCORRECTO
function getC() { ... }
function calc($id) { ... }
```

### 5. Validación en Múltiples Capas

- HTML5 (`required`, `type="email"`, `min`, `max`)
- PHP server-side (validar `$_POST`, `$_GET`)
- Base de datos (`NOT NULL`, `CHECK` constraints)

### 6. Manejo de Errores Informativo

```php
try {
    $cliente = obtenerClientePorId($id);
    if (!$cliente) {
        throw new Exception("Cliente no encontrado");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    exit("Error: " . htmlspecialchars($e->getMessage()));
}
```

### 7. Responsive Design

- Usar grid de Bootstrap (`col-12`, `col-md-6`)
- Navbar con `navbar-toggler` para móviles
- Cards adaptables
- Tablas responsivas: `<div class="table-responsive"><table>...`

### 8. Charset UTF-8 Consistente

```php
// PHP
<?php header('Content-Type: text/html; charset=utf-8'); ?>

// HTML
<meta charset="UTF-8">

// MySQL
$database->query("set names utf8;");

// Tabla
CREATE TABLE clientes(...) DEFAULT CHARSET=utf8mb4;
```

## Troubleshooting Común

### Error: PDOException Connection Failed

```php
// Verifica:
1. MySQL está corriendo
2. Credenciales correctas
3. Base de datos existe
4. Usuario tiene permisos

// Solución temporal (desarrollo):
$database = new PDO(
    'mysql:host=localhost;dbname=' . $dbName,
    $user,
    $password,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
```

### Error: Caracteres extraños (mojibake)

```php
// Solución: UTF-8 en todas partes
$database->query("set names utf8;");
header('Content-Type: text/html; charset=utf-8');
<meta charset="UTF-8">
```

### Error: Navbar cubre contenido

```css
/* estilo.css */
body {
  padding-top: 90px; /* Altura del navbar fixed-top */
}
```

### Validación HTML5 no funciona

```php
// Verifica:
1. Atributo 'required' presente
2. type="submit" en botón
3. No hay <button type="button">
4. Formulario tiene <form method="POST">
```

### Chart.js no se muestra

```javascript
// Debuggear:
console.log(datos);  // Verifica que los datos existen
console.log($grafica);  // Verifica que el canvas existe

// Asegura orden correcto:
1. HTML canvas
2. Script Chart.min.js
3. Script con new Chart()
```

## Checklist de Nuevo Proyecto

### Configuración Inicial

- [ ] Crear base de datos en MySQL
- [ ] Crear `esquema.sql` con tablas
- [ ] Crear `funciones.php` con `obtenerBD()`
- [ ] Crear `encabezado.php` con navbar y head
- [ ] Crear `pie.php` con cierre de tags
- [ ] Crear `estilo.css` con `body { padding-top: 90px; }`
- [ ] Crear `index.php` con redirect a dashboard

### Por Cada Entidad (ej: clientes)

- [ ] Funciones CRUD en `funciones.php`
- [ ] Vista listado: `clientes.php`
- [ ] Formulario agregar: `formulario_agregar_cliente.php`
- [ ] Controlador agregar: `guardar_cliente.php`
- [ ] Formulario editar: `formulario_editar_cliente.php`
- [ ] Controlador editar: `actualizar_cliente.php`
- [ ] Controlador eliminar: `eliminar_cliente.php`
- [ ] Dashboard individual: `dashboard_cliente.php` (opcional)

### Features Adicionales

- [ ] Búsqueda en listados
- [ ] Dashboard general con métricas
- [ ] Gráficos con Chart.js
- [ ] Iconos Material Design
- [ ] Confirmación en eliminaciones
- [ ] Validación HTML5 en formularios
- [ ] Sanitización con `htmlspecialchars()`

## Ejemplos de Proyectos

### 1. Sistema de Inventario

- Entidades: productos, categorías, proveedores, movimientos
- Dashboard: stock bajo, productos más vendidos
- Gráficos: ventas por categoría, movimientos por mes

### 2. Sistema de Citas

- Entidades: pacientes, doctores, citas, consultorios
- Dashboard: citas del día, pacientes atendidos
- Gráficos: citas por doctor, horarios más solicitados

### 3. Sistema de Biblioteca

- Entidades: libros, autores, préstamos, usuarios
- Dashboard: libros prestados, libros disponibles
- Gráficos: préstamos por mes, géneros más populares

## Recursos Adicionales

### Documentación

- **PHP PDO**: https://www.php.net/manual/es/book.pdo.php
- **Bootstrap 4**: https://getbootstrap.com/docs/4.6/
- **Chart.js**: https://www.chartjs.org/docs/latest/
- **Material Design Icons**: https://materialdesignicons.com/

### Librerías Locales Recomendadas

```
/css/bootstrap.min.css
/js/Chart.min.js
/js/jquery-3.4.1.min.js
```

### CDNs de Respaldo

```html
<!-- Bootstrap CSS -->
<link
  href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
  rel="stylesheet"
/>

<!-- Material Design Icons -->
<link
  href="https://cdn.jsdelivr.net/npm/@mdi/font@5.9.55/css/materialdesignicons.min.css"
  rel="stylesheet"
/>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
```

## Resumen Final

Este skill te guía para crear aplicaciones CRM robustas con:

✅ **Arquitectura clara**: MVC simplificado sin carpetas complejas  
✅ **Seguridad**: PDO prepared statements, validación, sanitización  
✅ **UI moderna**: Bootstrap 4 responsivo con componentes profesionales  
✅ **Visualización**: Chart.js para gráficos interactivos  
✅ **Convenciones**: Naming consistente en español  
✅ **Escalabilidad**: Patrón repository centralizado  
✅ **Best practices**: DRY, funciones atómicas, separación de concerns

**Recuerda**: Simplicidad es poder. Este enfoque procedural con funciones es perfecto para proyectos pequeños a medianos donde la velocidad de desarrollo y mantenibilidad son prioridad.
