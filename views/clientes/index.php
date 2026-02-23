<div class="row">
    <div class="col-12">
        <h1>Clientes</h1>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=cliente&action=crear" class="btn btn-success mb-2">
            <span class="mdi mdi-plus"></span> Agregar cliente
        </a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form method="GET" action="<?php echo BASE_URL; ?>/index.php">
            <input type="hidden" name="controller" value="cliente">
            <input type="hidden" name="action" value="index">
            <div class="form-row align-items-center">
                <div class="col-6 my-1">
                    <input class="form-control" type="text" name="nombre"
                        placeholder="Buscar por nombre"
                        value="<?php echo isset($busqueda) ? htmlspecialchars($busqueda) : ''; ?>">
                </div>
                <div class="col-auto my-1">
                    <button type="submit" class="btn btn-primary">
                        <span class="mdi mdi-magnify"></span> Buscar
                    </button>
                    <a href="<?php echo BASE_URL; ?>/index.php?controller=cliente&action=index" class="btn btn-secondary">
                        Limpiar
                    </a>
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
                        <th>Edad</th>
                        <th>Departamento</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clientes)): ?>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cliente->id); ?></td>
                                <td><?php echo htmlspecialchars($cliente->nombre); ?></td>
                                <td><?php echo htmlspecialchars($cliente->edad); ?></td>
                                <td><?php echo htmlspecialchars($cliente->departamento); ?></td>
                                <td><?php echo htmlspecialchars($cliente->fecha_registro); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/index.php?controller=cliente&action=dashboard&id=<?php echo $cliente->id; ?>"
                                        class="btn btn-info btn-sm">
                                        <span class="mdi mdi-chart-line"></span> Dashboard
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/index.php?controller=cliente&action=editar&id=<?php echo $cliente->id; ?>"
                                        class="btn btn-warning btn-sm">
                                        <span class="mdi mdi-pencil"></span> Editar
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/index.php?controller=cliente&action=eliminar&id=<?php echo $cliente->id; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('¿Está seguro de eliminar este cliente? Se eliminarán también todas sus ventas.');">
                                        <span class="mdi mdi-delete"></span> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay clientes registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>