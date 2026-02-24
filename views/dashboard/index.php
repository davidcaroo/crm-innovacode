<?php
$etapasOrden  = ['prospectado', 'contactado', 'negociacion', 'ganado', 'perdido'];
$etapasLabel  = ['Prospectado', 'Contactado', 'Negociacion', 'Ganado', 'Perdido'];
$etapaConteos = [];
foreach ($etapasOrden as $e) $etapaConteos[$e] = 0;
foreach ($empresasPorEtapa as $row) {
    if (isset($etapaConteos[$row->etapa_venta])) $etapaConteos[$row->etapa_venta] = (int)$row->conteo;
}
?>
<div class="page-header">
    <div>
        <h2 class="page-title"><span class="mdi mdi-desktop-mac-dashboard"></span> Dashboard</h2>
        <span class="page-subtitle"><?php echo date('d/m/Y'); ?></span>
    </div>
</div>

<div class="row mb-3">
    <div class="col-6 col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted mb-1" style="font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Total Empresas</div>
                <div style="font-size:2rem;font-weight:800;color:#1e40af;"><?php echo $totalEmpresas; ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted mb-1" style="font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Ultimos 30 Dias</div>
                <div style="font-size:2rem;font-weight:800;color:#0891b2;"><?php echo $empresasUltimos30Dias; ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted mb-1" style="font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Este Ano</div>
                <div style="font-size:2rem;font-weight:800;color:#7c3aed;"><?php echo $empresasAnioActual; ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted mb-1" style="font-size:0.8rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Ganadas</div>
                <div style="font-size:2rem;font-weight:800;color:#15803d;"><?php echo $totalVentas; ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3 px-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <strong style="font-size:0.92rem;"><span class="mdi mdi-filter-variant"></span> Pipeline por Etapa</strong>
                    <a href="<?php echo BASE_URL; ?>/index.php?controller=empresa&action=pipeline" class="btn btn-sm btn-outline-primary" style="font-size:0.82rem;">
                        <span class="mdi mdi-view-column"></span> Ver Kanban
                    </a>
                </div>
                <div class="row text-center">
                    <?php
                    $etapaColors = ['prospectado' => '#64748b', 'contactado' => '#d97706', 'negociacion' => '#ea580c', 'ganado' => '#16a34a', 'perdido' => '#dc2626'];
                    $etapaIcos   = ['prospectado' => 'mdi-magnify', 'contactado' => 'mdi-phone', 'negociacion' => 'mdi-handshake', 'ganado' => 'mdi-trophy', 'perdido' => 'mdi-close-circle'];
                    foreach ($etapasOrden as $i => $etapa):
                        $cnt = $etapaConteos[$etapa];
                    ?>
                        <div class="col mb-2">
                            <div class="p-2 rounded" style="background:<?php echo $etapaColors[$etapa]; ?>18;border:1px solid <?php echo $etapaColors[$etapa]; ?>33;">
                                <span class="mdi <?php echo $etapaIcos[$etapa]; ?>" style="font-size:1.4rem;color:<?php echo $etapaColors[$etapa]; ?>;"></span>
                                <div style="font-size:1.6rem;font-weight:800;color:<?php echo $etapaColors[$etapa]; ?>;"><?php echo $cnt; ?></div>
                                <div style="font-size:0.78rem;color:#64748b;font-weight:600;"><?php echo $etapasLabel[$i]; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12 col-md-5 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <strong style="font-size:0.9rem;">Empresas por Departamento</strong>
                    <span class="badge badge-light text-muted" style="font-size:0.7rem;">Resumen Directo</span>
                </div>

                <div class="row align-items-center">
                    <div class="col-6">
                        <canvas id="graficaDepartamentos" style="max-height:160px;"></canvas>
                    </div>
                    <div class="col-6">
                        <div style="max-height: 160px; overflow-y: auto; font-size: 0.82rem;">
                            <?php foreach ($empresasPorDepartamento as $d): ?>
                                <div class="d-flex justify-content-between mb-1 border-bottom pb-1">
                                    <span class="text-muted"><?php echo htmlspecialchars($d->departamento); ?>:</span>
                                    <strong style="color:#1e40af;"><?php echo $d->conteo; ?></strong>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-7 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <strong style="display:block;margin-bottom:12px;font-size:0.9rem;">Empresas Ganadas por Mes</strong>
                <canvas id="graficaEmpresasGanadas" style="max-height:220px;"></canvas>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($empresasPorActividad)): ?>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3">
            <strong style="display:block;margin-bottom:12px;font-size:0.9rem;">Resumen por Actividad Económica</strong>
            <div class="row">
                <?php foreach ($empresasPorActividad as $actividad): ?>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="d-flex align-items-center p-2 rounded" style="background:#f8fafc;border:1px solid #e4e8f0;">
                            <div style="font-size:1.1rem;font-weight:800;color:#1e40af;min-width:30px;"><?php echo $actividad->conteo; ?></div>
                            <div style="font-size:0.75rem;color:#475569;line-height:1.2; font-weight: 500;">
                                <?php echo htmlspecialchars($actividad->actividad_economica); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    (function() {
        const deptData = <?php echo json_encode(array_values(array_map(function ($d) {
                                return ['label' => $d->departamento, 'value' => (int)$d->conteo];
                            }, $empresasPorDepartamento))); ?>;
        const ganadas = <?php echo json_encode(array_values(array_map(function ($v) {
                            return ['mes' => (int)$v->mes, 'total' => (int)$v->total];
                        }, $empresasGanadas))); ?>;
        const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        const palette = ['#3b82f6', '#f59e0b', '#22c55e', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#0ea5e9', '#ec4899', '#84cc16'];

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
                    cutoutPercentage: 50
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
                        backgroundColor: 'rgba(34,197,94,0.75)',
                        borderColor: '#15803d',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1,
                                fontColor: '#64748b'
                            },
                            gridLines: {
                                color: '#f1f5f9'
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                fontColor: '#64748b'
                            },
                            gridLines: {
                                display: false
                            }
                        }]
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        backgroundColor: '#1e293b',
                        titleFontSize: 13,
                        bodyFontSize: 12,
                        xPadding: 10,
                        yPadding: 10,
                        cornerRadius: 4,
                        displayColors: false
                    }
                }
            });
        }
    })();
</script>