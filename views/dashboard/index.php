<?php
$etapasOrden  = ['prospectado', 'contactado', 'negociacion', 'ganado', 'perdido'];
$etapasLabel  = ['Prospectado', 'Contactado', 'Negociacion', 'Ganado', 'Perdido'];
$etapaConteos = [];
foreach ($etapasOrden as $e) {
    $etapaConteos[$e] = 0;
}
foreach ($empresasPorEtapa as $row) {
    if (isset($etapaConteos[$row->etapa_venta])) {
        $etapaConteos[$row->etapa_venta] = (int)$row->conteo;
    }
}
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <p class="mb-0 small text-muted"><?php echo date('d/m/Y'); ?></p>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Empresas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalEmpresas; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ultimos 30 dias</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $empresasUltimos30Dias; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Este ano</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $empresasAnioActual; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ganadas</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalVentas; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Empresas Ganadas por Mes</h6>
            </div>
            <div class="card-body">
                <div style="height: 280px;">
                    <canvas id="graficaEmpresasGanadas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Pipeline por Etapa</h6>
            </div>
            <div class="card-body">
                <?php foreach ($etapasOrden as $i => $etapa): ?>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-gray-700"><?php echo $etapasLabel[$i]; ?></span>
                        <span class="badge badge-light"><?php echo $etapaConteos[$etapa]; ?></span>
                    </div>
                <?php endforeach; ?>
                <hr>
                <a href="<?php echo url('empresa/pipeline'); ?>" class="btn btn-sm btn-primary btn-block">
                    <i class="fas fa-columns mr-1"></i> Ver Kanban
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 mb-4">
        <div class="card shadow mb-4 h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Empresas por Departamento</h6>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <canvas id="graficaDepartamentos" style="max-height: 220px;"></canvas>
                    </div>
                    <div class="col-md-6">
                        <?php foreach ($empresasPorDepartamento as $d): ?>
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span class="small text-muted"><?php echo htmlspecialchars($d->departamento); ?></span>
                                <span class="font-weight-bold text-primary"><?php echo (int)$d->conteo; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($empresasPorActividad)): ?>
        <div class="col-xl-6 mb-4">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Resumen por Actividad Económica</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($empresasPorActividad as $actividad): ?>
                            <div class="col-sm-6 mb-3">
                                <div class="border rounded p-2 h-100">
                                    <div class="h6 mb-1 text-primary"><?php echo (int)$actividad->conteo; ?></div>
                                    <div class="small text-muted"><?php echo htmlspecialchars($actividad->actividad_economica); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    (function() {
        const deptData = <?php echo json_encode(array_values(array_map(function ($d) {
                                return ['label' => $d->departamento, 'value' => (int)$d->conteo];
                            }, $empresasPorDepartamento))); ?>;
        const ganadas = <?php echo json_encode(array_values(array_map(function ($v) {
                            return ['mes' => (int)$v->mes, 'total' => (int)$v->total];
                        }, $empresasGanadas))); ?>;
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        const palette = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#20c9a6', '#fd7e14'];

        const ctxDept = document.getElementById('graficaDepartamentos');
        if (ctxDept && deptData.length) {
            new Chart(ctxDept, {
                type: 'doughnut',
                data: {
                    labels: deptData.map(d => d.label),
                    datasets: [{
                        data: deptData.map(d => d.value),
                        backgroundColor: palette,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            fontSize: 11
                        }
                    },
                    cutoutPercentage: 55
                }
            });
        }

        const ctxGan = document.getElementById('graficaEmpresasGanadas');
        if (ctxGan) {
            const vals = new Array(12).fill(0);
            ganadas.forEach(g => {
                vals[g.mes - 1] = g.total;
            });
            new Chart(ctxGan, {
                type: 'bar',
                data: {
                    labels: meses,
                    datasets: [{
                        label: 'Ganadas',
                        data: vals,
                        backgroundColor: 'rgba(28, 200, 138, 0.75)',
                        borderColor: '#1cc88a',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }]
                    }
                }
            });
        }
    })();
</script>