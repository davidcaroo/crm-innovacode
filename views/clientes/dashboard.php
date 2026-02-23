<div class="row">
    <div class="col-12">
        <h1>Dashboard de <?php echo htmlspecialchars($cliente->nombre); ?></h1>
        <a href="<?php echo BASE_URL; ?>/index.php?controller=cliente&action=index" class="btn btn-info mb-2">
            <span class="mdi mdi-arrow-left"></span> Volver
        </a>
    </div>
</div>

<div class="row">
    <div class="col-3">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">$<?php echo number_format($totalVentas, 2); ?></h1>
                <h6 class="card-subtitle mb-2 text-muted">Total de ventas</h6>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">$<?php echo number_format($totalMes, 2); ?></h1>
                <h6 class="card-subtitle mb-2 text-muted">Ventas del mes</h6>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">$<?php echo number_format($totalAnio, 2); ?></h1>
                <h6 class="card-subtitle mb-2 text-muted">Ventas del año</h6>
            </div>
        </div>
    </div>
    <div class="col-3">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">$<?php echo number_format($totalAniosAnteriores, 2); ?></h1>
                <h6 class="card-subtitle mb-2 text-muted">Años anteriores</h6>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <h3>Información del Cliente</h3>
        <table class="table table-bordered">
            <tr>
                <th>Nombre:</th>
                <td><?php echo htmlspecialchars($cliente->nombre); ?></td>
            </tr>
            <tr>
                <th>Edad:</th>
                <td><?php echo htmlspecialchars($cliente->edad); ?></td>
            </tr>
            <tr>
                <th>Departamento:</th>
                <td><?php echo htmlspecialchars($cliente->departamento); ?></td>
            </tr>
            <tr>
                <th>Fecha de registro:</th>
                <td><?php echo htmlspecialchars($cliente->fecha_registro); ?></td>
            </tr>
        </table>
    </div>
</div>

<?php if (!empty($ventas)): ?>
    <div class="row mt-4">
        <div class="col-12">
            <h3>Historial de Ventas</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Monto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $venta): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($venta->fecha); ?></td>
                            <td>$<?php echo number_format($venta->monto, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>