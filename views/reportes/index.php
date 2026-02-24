<?php
// views/reportes/index.php
?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="mdi mdi-chart-areaspline mr-2 text-primary"></i>Panel de Inteligencia y Reportes</h2>
        <div>
            <button onclick="window.print()" class="btn btn-sm btn-success shadow-sm" style="font-weight:600; border-radius:8px;">
                <i class="mdi mdi-printer mr-1" style="font-size:1.2rem;"></i>Imprimir Reporte
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row">
        <!-- Tasa de Conversión -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Oportunidades Ganadas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $ganados = 0;
                                foreach ($stats['conversion'] as $c) if ($c->etapa_venta == 'ganado') $ganados = $c->cantidad;
                                echo $ganados;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="mdi mdi-trophy-variant mdi-36px text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Ventas Acumuladas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Ventas Totales (Año)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?php
                                    $total = 0;
                                    foreach ($stats['ventas_mes'] as $v) $total += $v->total;
                                    echo number_format($total, 0, ',', '.');
                                    ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="mdi mdi-currency-usd mdi-36px text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividades Realizadas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Gestiones Realizadas
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                        <?php
                                        $act = 0;
                                        foreach ($stats['actividades'] as $a) $act += $a->cantidad;
                                        echo $act;
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="mdi mdi-calendar-check mdi-36px text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversión Avg -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                % Conversión Éxito</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $totalEmp = 0;
                                foreach ($stats['conversion'] as $c) $totalEmp += $c->cantidad;
                                echo $totalEmp > 0 ? round(($ganados / $totalEmp) * 100, 1) : 0;
                                ?>%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="mdi mdi-percent mdi-36px text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico Ventas -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Crecimiento de Ventas Mensuales</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="ventasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Embudo de Conversión -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pipeline de Oportunidades</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4" style="height: 260px;">
                        <canvas id="pipelieChart"></canvas>
                    </div>
                    <hr>
                    <div class="small text-muted mt-2">Distribución actual de prospectos por etapa.</div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($_SESSION['usuario_rol'] !== 'usuario'): ?>
        <!-- Ranking de Vendedores -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4 border-bottom-primary">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ranking de Vendedores - Rendimiento Monetario</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead class="bg-light">
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
                                                <div class="progress">
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
</div>

<!-- JS para Gráficos -->
<script src="<?php echo BASE_URL; ?>/public/js/Chart.min.js"></script>
<script>
    // Chart Ventas
    var ctxV = document.getElementById('ventasChart').getContext('2d');
    new Chart(ctxV, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labelsVentas); ?>,
            datasets: [{
                label: "Monto ($)",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                data: <?php echo json_encode($dataVentas); ?>,
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value, index, values) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }]
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