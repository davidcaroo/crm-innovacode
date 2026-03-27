<?php
// views/reportes/index.php
$ganados = 0;
foreach ($stats['conversion'] as $c) {
    if ($c->etapa_venta == 'ganado') {
        $ganados = $c->cantidad;
    }
}

$total = 0;
foreach ($stats['ventas_mes'] as $v) {
    $total += $v->total;
}

$act = 0;
foreach ($stats['actividades'] as $a) {
    $act += $a->cantidad;
}

$totalEmp = 0;
foreach ($stats['conversion'] as $c) {
    $totalEmp += $c->cantidad;
}
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Panel de Inteligencia y Reportes</h1>
        <p class="mb-0 small text-muted">Indicadores de desempeño comercial y evolución del pipeline</p>
    </div>
    <button onclick="window.print()" class="btn btn-success btn-sm">
        <i class="fas fa-print mr-1"></i> Imprimir Reporte
    </button>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Oportunidades Ganadas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $ganados; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ventas Totales (Año)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo number_format($total, 0, ',', '.'); ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Gestiones Realizadas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $act; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">% Conversión Éxito</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalEmp > 0 ? round(($ganados / $totalEmp) * 100, 1) : 0; ?>%</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Crecimiento de Ventas Mensuales</h6>
            </div>
            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="ventasChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pipeline de Oportunidades</h6>
            </div>
            <div class="card-body">
                <div class="pt-2" style="height: 260px;">
                    <canvas id="pipelieChart"></canvas>
                </div>
                <hr>
                <div class="small text-muted mt-2">Distribución actual de prospectos por etapa.</div>
            </div>
        </div>
    </div>
</div>

<?php if ($_SESSION['usuario_rol'] !== 'usuario'): ?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ranking de Vendedores - Rendimiento Monetario</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Posición</th>
                                    <th>Asesor Comercial</th>
                                    <th>Operaciones</th>
                                    <th>Total Recaudado</th>
                                    <th>Progreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['ranking'] as $index => $r): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td class="font-weight-bold"><?= htmlspecialchars($r->nombre) ?></td>
                                        <td><?= $r->num_operaciones ?></td>
                                        <td class="text-success font-weight-bold">$<?= number_format($r->total_ventas, 0, ',', '.') ?></td>
                                        <td>
                                            <div class="progress" style="height: 0.6rem;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- JS para Gráficos -->
<script src="<?php echo BASE_URL; ?>/public/js/Chart.min.js"></script>
<script>
    // Chart Ventas — mejorada: línea con puntos, delta y formato COP
    var ventasLabels = <?php echo json_encode($labelsVentas); ?>;
    var ventasData = <?php echo json_encode($dataVentas); ?>;
    // asegurar que sean números (Chart.js v3 espera números en parsed)
    ventasData = ventasData.map(function(v) {
        if (v === null || v === undefined || v === '') return NaN;
        return Number(v);
    });
    var ctxV = document.getElementById('ventasChart').getContext('2d');

    // filtrar NaN para calcular min/max
    var numeric = ventasData.filter(function(x) {
        return isFinite(x);
    });
    var minV = numeric.length ? Math.min.apply(null, numeric) : 0;
    var maxV = numeric.length ? Math.max.apply(null, numeric) : 0;
    var padding = (maxV - minV) * 0.15 || (maxV * 0.1);

    new Chart(ctxV, {
        type: 'line',
        data: {
            labels: ventasLabels,
            datasets: [{
                label: 'Monto (COP)',
                data: ventasData,
                backgroundColor: 'rgba(78, 115, 223, 0.06)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointRadius: 6,
                pointHoverRadius: 8,
                pointBackgroundColor: function(context) {
                    var idx = context.dataIndex;
                    if (idx === 0) return 'rgba(78, 115, 223, 1)';
                    var cur = ventasData[idx];
                    var prev = ventasData[idx - 1];
                    if (!isFinite(cur) || !isFinite(prev)) return 'rgba(78, 115, 223, 1)';
                    return cur >= prev ? 'rgba(28, 200, 138, 1)' : 'rgba(231, 74, 59, 1)';
                },
                fill: true,
                tension: 0.2,
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            var nf = new Intl.NumberFormat('es-CO');
                            var value = context.parsed && context.parsed.y !== undefined ? context.parsed.y : context.parsed;
                            var idx = context.dataIndex;
                            var prev = idx > 0 ? ventasData[idx - 1] : null;
                            var diff = (isFinite(value) && isFinite(prev)) ? (value - prev) : null;
                            var pct = (isFinite(prev) && diff !== null) ? (diff / prev * 100) : null;
                            var s = ' $' + (isFinite(value) ? nf.format(value) : value);
                            if (diff !== null) {
                                var sign = diff >= 0 ? '+' : '';
                                s += ' ( ' + sign + nf.format(diff) + ', ' + sign + (pct !== null ? pct.toFixed(1) + '%' : '') + ')';
                            }
                            return s;
                        }
                    }
                },
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false
                    }
                },
                y: {
                    ticks: {
                        beginAtZero: false,
                        suggestedMin: Math.max(0, minV - padding),
                        suggestedMax: maxV + padding,
                        callback: function(value) {
                            return '$' + Number(value).toLocaleString('es-CO');
                        }
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                }
            }
        }
    });

    // Chart Pipeline (Doughnut)
    var ctxP = document.getElementById('pipelieChart').getContext('2d');
    new Chart(ctxP, {
        type: 'doughnut',
        data: {
            labels: [<?php foreach ($stats['conversion'] as $c) echo '"' . ucfirst($c->etapa_venta) . '",'; ?>],
            datasets: [{
                data: [<?php foreach ($stats['conversion'] as $c) echo $c->cantidad . ','; ?>],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutoutPercentage: 70,
            legend: {
                display: false
            }
        }
    });
</script>