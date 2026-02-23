# Ejemplo: Módulo de Productos

Este ejemplo demuestra cómo implementar un módulo CRUD completo siguiendo el skill `php-bootstrap-crm-developer`.

## Archivos Necesarios

```
├── funciones.php (agregar funciones)
├── productos.php (listado)
├── formulario_agregar_producto.php
├── guardar_producto.php
├── formulario_editar_producto.php
├── actualizar_producto.php
├── eliminar_producto.php
└── dashboard_producto.php (opcional)
```

## 1. Schema SQL

```sql
-- Agregar a esquema.sql
CREATE TABLE IF NOT EXISTS productos(
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(9,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    categoria VARCHAR(100) NOT NULL,
    fecha_registro DATE NOT NULL
);
```

## 2. Funciones en funciones.php

```php
// Agregar al final de funciones.php

function obtenerCategorias()
{
    return [
        "Electrónica",
        "Ropa",
        "Alimentos",
        "Hogar",
        "Deportes",
    ];
}

function agregarProducto($nombre, $descripcion, $precio, $stock, $categoria)
{
    $bd = obtenerBD();
    $fechaRegistro = date("Y-m-d");
    $sentencia = $bd->prepare("INSERT INTO productos(nombre, descripcion, precio, stock, categoria, fecha_registro) 
                               VALUES (?, ?, ?, ?, ?, ?)");
    return $sentencia->execute([$nombre, $descripcion, $precio, $stock, $categoria, $fechaRegistro]);
}

function obtenerProductos()
{
    $bd = obtenerBD();
    $sentencia = $bd->query("SELECT id, nombre, descripcion, precio, stock, categoria, fecha_registro 
                             FROM productos 
                             ORDER BY fecha_registro DESC");
    return $sentencia->fetchAll();
}

function obtenerProductoPorId($id)
{
    $bd = obtenerBD();
    $sentencia = $bd->prepare("SELECT id, nombre, descripcion, precio, stock, categoria, fecha_registro 
                               FROM productos 
                               WHERE id = ?");
    $sentencia->execute([$id]);
    return $sentencia->fetchObject();
}

function actualizarProducto($nombre, $descripcion, $precio, $stock, $categoria, $id)
{
    $bd = obtenerBD();
    $sentencia = $bd->prepare("UPDATE productos 
                               SET nombre = ?, descripcion = ?, precio = ?, stock = ?, categoria = ? 
                               WHERE id = ?");
    return $sentencia->execute([$nombre, $descripcion, $precio, $stock, $categoria, $id]);
}

function eliminarProducto($id)
{
    $bd = obtenerBD();
    $sentencia = $bd->prepare("DELETE FROM productos WHERE id = ?");
    return $sentencia->execute([$id]);
}

function buscarProductos($nombre)
{
    $bd = obtenerBD();
    $sentencia = $bd->prepare("SELECT id, nombre, descripcion, precio, stock, categoria, fecha_registro 
                               FROM productos 
                               WHERE nombre LIKE ?");
    $sentencia->execute(["%$nombre%"]);
    return $sentencia->fetchAll();
}

function obtenerProductosPorCategoria()
{
    $bd = obtenerBD();
    $sentencia = $bd->query("SELECT categoria, COUNT(*) AS conteo 
                             FROM productos 
                             GROUP BY categoria");
    return $sentencia->fetchAll();
}

function obtenerProductosStockBajo($minimo = 10)
{
    $bd = obtenerBD();
    $sentencia = $bd->prepare("SELECT id, nombre, stock 
                               FROM productos 
                               WHERE stock <= ? 
                               ORDER BY stock ASC");
    $sentencia->execute([$minimo]);
    return $sentencia->fetchAll();
}

function valorTotalInventario()
{
    $bd = obtenerBD();
    $sentencia = $bd->query("SELECT COALESCE(SUM(precio * stock), 0) AS total FROM productos");
    return $sentencia->fetchObject()->total;
}
```

## 3. Vista de Listado (productos.php)

```php
<?php
include_once "funciones.php";

// Búsqueda
if (isset($_GET["nombre"]) && !empty($_GET["nombre"])) {
    $productos = buscarProductos($_GET["nombre"]);
} else {
    $productos = obtenerProductos();
}
?>

<?php include_once "encabezado.php" ?>

<div class="row">
    <div class="col-12">
        <h1>Productos</h1>
        <a href="./formulario_agregar_producto.php" class="btn btn-success mb-2">
            <i class="mdi mdi-plus"></i> Agregar producto
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form method="GET" action="productos.php">
            <div class="form-row align-items-center">
                <div class="col-6 my-1">
                    <input class="form-control" type="text" name="nombre" 
                           placeholder="Buscar por nombre"
                           value="<?php echo isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : '' ?>">
                </div>
                <div class="col-auto my-1">
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-magnify"></i> Buscar
                    </button>
                    <a href="./productos.php" class="btn btn-secondary">Limpiar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Categoría</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto->id) ?></td>
                            <td><?php echo htmlspecialchars($producto->nombre) ?></td>
                            <td><?php echo htmlspecialchars($producto->descripcion) ?></td>
                            <td>$<?php echo number_format($producto->precio, 2) ?></td>
                            <td>
                                <?php if ($producto->stock <= 10) { ?>
                                    <span class="badge badge-danger"><?php echo htmlspecialchars($producto->stock) ?></span>
                                <?php } else { ?>
                                    <?php echo htmlspecialchars($producto->stock) ?>
                                <?php } ?>
                            </td>
                            <td><?php echo htmlspecialchars($producto->categoria) ?></td>
                            <td>
                                <a href="./formulario_editar_producto.php?id=<?php echo $producto->id ?>" 
                                   class="btn btn-warning btn-sm">
                                    <i class="mdi mdi-pencil"></i> Editar
                                </a>
                                <a href="./eliminar_producto.php?id=<?php echo $producto->id ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('¿Está seguro de eliminar este producto?')">
                                    <i class="mdi mdi-delete"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include_once "pie.php" ?>
```

## 4. Formulario Agregar (formulario_agregar_producto.php)

```php
<?php
include_once "funciones.php";
$categorias = obtenerCategorias();
?>

<?php include_once "encabezado.php" ?>

<div class="row">
    <div class="col-12">
        <h1>Agregar Producto</h1>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form method="POST" action="guardar_producto.php">
            <div class="form-group">
                <label for="nombre">Nombre del Producto *</label>
                <input required type="text" class="form-control" 
                       name="nombre" id="nombre" 
                       placeholder="Ej: Laptop HP 15">
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea class="form-control" name="descripcion" id="descripcion" 
                          rows="3" placeholder="Descripción detallada del producto"></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="precio">Precio *</label>
                    <input required type="number" step="0.01" min="0" class="form-control" 
                           name="precio" id="precio" placeholder="0.00">
                </div>
                
                <div class="form-group col-md-4">
                    <label for="stock">Stock *</label>
                    <input required type="number" min="0" class="form-control" 
                           name="stock" id="stock" placeholder="0">
                </div>
                
                <div class="form-group col-md-4">
                    <label for="categoria">Categoría *</label>
                    <select required class="form-control" name="categoria" id="categoria">
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categorias as $categoria) { ?>
                            <option value="<?php echo htmlspecialchars($categoria) ?>">
                                <?php echo htmlspecialchars($categoria) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success">
                <i class="mdi mdi-content-save"></i> Guardar
            </button>
            <a href="./productos.php" class="btn btn-info">
                <i class="mdi mdi-arrow-left"></i> Cancelar
            </a>
        </form>
    </div>
</div>

<?php include_once "pie.php" ?>
```

## 5. Controlador Guardar (guardar_producto.php)

```php
<?php
include_once "funciones.php";

if (!isset($_POST["nombre"]) || !isset($_POST["precio"]) || !isset($_POST["stock"]) || !isset($_POST["categoria"])) {
    exit("Faltan datos obligatorios");
}

$nombre = $_POST["nombre"];
$descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : "";
$precio = (float)$_POST["precio"];
$stock = (int)$_POST["stock"];
$categoria = $_POST["categoria"];

// Validaciones
if (empty($nombre)) {
    exit("El nombre es obligatorio");
}
if ($precio < 0) {
    exit("El precio no puede ser negativo");
}
if ($stock < 0) {
    exit("El stock no puede ser negativo");
}

$ok = agregarProducto($nombre, $descripcion, $precio, $stock, $categoria);

if (!$ok) {
    echo "Error al registrar el producto.";
} else {
    header("Location: productos.php");
}
```

## 6. Formulario Editar (formulario_editar_producto.php)

```php
<?php
include_once "funciones.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    exit("ID no válido");
}

$id = (int)$_GET["id"];
$producto = obtenerProductoPorId($id);

if (!$producto) {
    exit("Producto no encontrado");
}

$categorias = obtenerCategorias();
?>

<?php include_once "encabezado.php" ?>

<div class="row">
    <div class="col-12">
        <h1>Editar Producto</h1>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form method="POST" action="actualizar_producto.php">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($producto->id) ?>">
            
            <div class="form-group">
                <label for="nombre">Nombre del Producto *</label>
                <input required type="text" class="form-control" 
                       name="nombre" id="nombre" 
                       value="<?php echo htmlspecialchars($producto->nombre) ?>">
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea class="form-control" name="descripcion" id="descripcion" 
                          rows="3"><?php echo htmlspecialchars($producto->descripcion) ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="precio">Precio *</label>
                    <input required type="number" step="0.01" min="0" class="form-control" 
                           name="precio" id="precio" 
                           value="<?php echo htmlspecialchars($producto->precio) ?>">
                </div>
                
                <div class="form-group col-md-4">
                    <label for="stock">Stock *</label>
                    <input required type="number" min="0" class="form-control" 
                           name="stock" id="stock" 
                           value="<?php echo htmlspecialchars($producto->stock) ?>">
                </div>
                
                <div class="form-group col-md-4">
                    <label for="categoria">Categoría *</label>
                    <select required class="form-control" name="categoria" id="categoria">
                        <?php foreach ($categorias as $categoria) { ?>
                            <option value="<?php echo htmlspecialchars($categoria) ?>"
                                    <?php echo ($producto->categoria === $categoria) ? 'selected' : '' ?>>
                                <?php echo htmlspecialchars($categoria) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-success">
                <i class="mdi mdi-content-save"></i> Actualizar
            </button>
            <a href="./productos.php" class="btn btn-info">
                <i class="mdi mdi-arrow-left"></i> Cancelar
            </a>
        </form>
    </div>
</div>

<?php include_once "pie.php" ?>
```

## 7. Controlador Actualizar (actualizar_producto.php)

```php
<?php
include_once "funciones.php";

if (!isset($_POST["id"]) || !isset($_POST["nombre"]) || !isset($_POST["precio"]) 
    || !isset($_POST["stock"]) || !isset($_POST["categoria"])) {
    exit("Faltan datos obligatorios");
}

$id = (int)$_POST["id"];
$nombre = $_POST["nombre"];
$descripcion = isset($_POST["descripcion"]) ? $_POST["descripcion"] : "";
$precio = (float)$_POST["precio"];
$stock = (int)$_POST["stock"];
$categoria = $_POST["categoria"];

// Validaciones
if (empty($nombre)) {
    exit("El nombre es obligatorio");
}
if ($precio < 0) {
    exit("El precio no puede ser negativo");
}
if ($stock < 0) {
    exit("El stock no puede ser negativo");
}

$ok = actualizarProducto($nombre, $descripcion, $precio, $stock, $categoria, $id);

if (!$ok) {
    echo "Error al actualizar el producto.";
} else {
    header("Location: productos.php");
}
```

## 8. Controlador Eliminar (eliminar_producto.php)

```php
<?php
include_once "funciones.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    exit("ID no válido");
}

$id = (int)$_GET["id"];

$ok = eliminarProducto($id);

if ($ok) {
    header("Location: productos.php");
} else {
    echo "Error al eliminar el producto.";
}
```

## 9. Dashboard de Productos (dashboard_producto.php)

```php
<?php
include_once "funciones.php";

$totalProductos = count(obtenerProductos());
$valorInventario = number_format(valorTotalInventario(), 2);
$productosPorCategoria = obtenerProductosPorCategoria();
$productosStockBajo = obtenerProductosStockBajo(10);
?>

<?php include_once "encabezado.php" ?>

<div class="row">
    <div class="col-12">
        <h1 class="text-center">Dashboard de Productos</h1>
    </div>
</div>

<div class="row">
    <div class="col-4">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title"><?php echo $totalProductos ?></h1>
                <h6 class="card-subtitle mb-2 text-muted">Total de Productos</h6>
            </div>
        </div>
    </div>
    
    <div class="col-4">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">$<?php echo $valorInventario ?></h1>
                <h6 class="card-subtitle mb-2 text-muted">Valor Total Inventario</h6>
            </div>
        </div>
    </div>
    
    <div class="col-4">
        <div class="card bg-warning">
            <div class="card-body">
                <h1 class="card-title"><?php echo count($productosStockBajo) ?></h1>
                <h6 class="card-subtitle mb-2 text-white">Productos con Stock Bajo</h6>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-6">
        <h3>Productos por Categoría</h3>
        <canvas id="graficaCategorias"></canvas>
    </div>
    
    <div class="col-6">
        <h3>Productos con Stock Bajo</h3>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productosStockBajo as $producto) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto->nombre) ?></td>
                        <td><span class="badge badge-danger"><?php echo htmlspecialchars($producto->stock) ?></span></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="./js/Chart.min.js"></script>
<script>
    const productosPorCategoria = <?php echo json_encode($productosPorCategoria) ?>;
    
    const $grafica = document.getElementById("graficaCategorias");
    
    new Chart($grafica, {
        type: 'pie',
        data: {
            labels: productosPorCategoria.map(d => d.categoria),
            datasets: [{
                data: productosPorCategoria.map(d => d.conteo),
                backgroundColor: [
                    'rgba(163,221,203,0.6)',
                    'rgba(232,233,161,0.6)',
                    'rgba(230,181,102,0.6)',
                    'rgba(229,112,126,0.6)',
                    'rgba(102,187,230,0.6)',
                ]
            }]
        }
    });
</script>

<?php include_once "pie.php" ?>
```

## 10. Agregar al Navbar (encabezado.php)

```php
<!-- Agregar en el navbar -->
<li class="nav-item">
    <a class="nav-link" href="./productos.php">
        <i class="mdi mdi-package-variant"></i> Productos
    </a>
</li>
```

## Resumen

Este ejemplo completo muestra:

✅ **CRUD completo** con 7 archivos  
✅ **Búsqueda** con filtro  
✅ **Validaciones** HTML5 y PHP  
✅ **Dashboard** con métricas y gráficos  
✅ **Alertas visuales** para stock bajo  
✅ **Patrones de seguridad** (htmlspecialchars, prepared statements)  
✅ **UI Bootstrap** responsiva  
✅ **Iconos** Material Design  

Sigue estos mismos patrones para cualquier módulo nuevo (proveedores, empleados, pedidos, etc.).
