<?php
$etapasOrden  = ['prospectado', 'contactado', 'negociacion', 'seguimiento', 'ganado', 'perdido'];
$etapasLabel  = ['Prospectado', 'Contactado', 'Negociacion', 'Seguimiento', 'Ganado', 'Perdido'];
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
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Gestiones Ultimos 30 dias</div>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total de gestiones en el año</div>
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
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Gestiones Ganadas</div>
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
                <h6 class="m-0 font-weight-bold text-primary">Gestiones/Empresas Ganadas por Mes</h6>
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
                <h6 class="m-0 font-weight-bold text-primary">Resumen Pipeline por Etapa</h6>
            </div>
            <div class="card-body">
                <?php 
                $ordenPipelineView = [
                    'prospectado' => 'Prospectado',
                    'investigacion' => 'Investigación',
                    'total_contactados' => 'Contactos Efectivos',
                    'contacto_interesado' => 'Contacto Interesado',
                    'estudio_necesidades' => 'Estudio de necesidades',
                    'oferta_servicios' => 'Oferta de Servicios',
                    'seguimiento_oferta' => 'Seguimiento a la oferta',
                    'perdidos' => 'Cierre fallido',
                    'cierre_exitoso' => 'Cierre exitoso'
                ];
                
                foreach ($ordenPipelineView as $key => $label): ?>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small text-gray-700"><?php echo $label; ?></span>
                        <span class="badge badge-light"><?php echo $consolidadoPipeline[$key] ?? 0; ?></span>
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
                        <div id="mapaDepartamentos" style="height: 260px; border-radius: 8px; overflow: hidden;"></div>
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

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="<?php echo BASE_URL; ?>/public/js/Chart.min.js"></script>
<style>
    .dept-marker {
        background: #2e59d9;
        color: #fff;
        border-radius: 999px;
        min-width: 22px;
        height: 22px;
        line-height: 22px;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
        border: 2px solid #fff;
        padding: 0 6px;
    }
</style>

<script>
    (function() {
        const deptData = <?php echo json_encode(array_values(array_map(function ($d) {
                                return ['label' => $d->departamento, 'value' => (int)$d->conteo];
                            }, $empresasPorDepartamento))); ?>;
        const ganadas = <?php echo json_encode(array_values(array_map(function ($v) {
                            return ['mes' => (int)$v->mes, 'total' => (int)$v->total];
                        }, $empresasGanadas))); ?>;
        const perdidas = <?php echo json_encode(array_values(array_map(function ($v) {
                                return ['mes' => (int)$v->mes, 'total' => (int)$v->total];
                            }, $empresasPerdidas ?? []))); ?>;
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        const palette = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#20c9a6', '#fd7e14'];

        const norm = (txt) => {
            let s = (txt || '').toString();
            if (typeof s.normalize === 'function') {
                s = s.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            }
            return s.toUpperCase().trim();
        };

        const deptCoords = {
            'AMAZONAS': [-4.2153, -69.9406],
            'ANTIOQUIA': [6.2518, -75.5636],
            'ARAUCA': [7.0847, -70.7591],
            'ATLANTICO': [10.9685, -74.7813],
            'BOLIVAR': [10.3910, -75.4794],
            'BOYACA': [5.5353, -73.3678],
            'CALDAS': [5.0703, -75.5138],
            'CAQUETA': [1.6154, -75.6043],
            'CASANARE': [5.3378, -72.3959],
            'CAUCA': [2.4448, -76.6147],
            'CESAR': [10.4631, -73.2532],
            'CHOCO': [5.6947, -76.6611],
            'CORDOBA': [8.7509, -75.8814],
            'CUNDINAMARCA': [4.7110, -74.0721],
            'BOGOTA': [4.7110, -74.0721],
            'BOGOTA D.C.': [4.7110, -74.0721],
            'GUAINIA': [2.5729, -72.6459],
            'GUAVIARE': [2.5729, -72.6459],
            'HUILA': [2.9386, -75.2819],
            'LA GUAJIRA': [11.5444, -72.9064],
            'MAGDALENA': [11.2408, -74.1990],
            'META': [4.1420, -73.6266],
            'NARINO': [1.2059, -77.2858],
            'NORTE DE SANTANDER': [7.8891, -72.4967],
            'PUTUMAYO': [0.5051, -76.4957],
            'QUINDIO': [4.5339, -75.6811],
            'RISARALDA': [4.8143, -75.6946],
            'SAN ANDRES': [12.5847, -81.7006],
            'SANTANDER': [7.1254, -73.1198],
            'SUCRE': [9.3047, -75.3978],
            'TOLIMA': [4.4389, -75.2322],
            'VALLE DEL CAUCA': [3.4516, -76.5320],
            'VAUPES': [0.8554, -70.8110],
            'VICHADA': [5.6947, -67.4917]
        };

        const mapaEl = document.getElementById('mapaDepartamentos');
        if (mapaEl && deptData.length && window.L) {
            const mapa = L.map('mapaDepartamentos', {
                zoomControl: true,
                attributionControl: false,
                scrollWheelZoom: false
            }).setView([4.5, -74.1], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 8,
                minZoom: 4
            }).addTo(mapa);

            deptData.forEach((item) => {
                const key = norm(item.label);
                const coords = deptCoords[key];
                if (!coords) return;

                const icon = L.divIcon({
                    className: '',
                    html: '<div class="dept-marker">' + item.value + '</div>',
                    iconSize: [26, 26],
                    iconAnchor: [13, 13]
                });

                L.marker(coords, {
                        icon
                    })
                    .addTo(mapa)
                    .bindPopup('<strong>' + item.label + '</strong><br>Total empresas: ' + item.value);
            });

            setTimeout(() => mapa.invalidateSize(), 150);
        }

        const ctxGan = document.getElementById('graficaEmpresasGanadas');
        if (ctxGan && window.Chart) {
            const vals = new Array(12).fill(0);
            const valsPerdidas = new Array(12).fill(0);
            ganadas.forEach(g => {
                vals[g.mes - 1] = g.total;
            });
            perdidas.forEach(p => {
                valsPerdidas[p.mes - 1] = p.total;
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
                    }, {
                        label: 'Perdidas',
                        data: valsPerdidas,
                        backgroundColor: 'rgba(231, 74, 59, 0.75)',
                        borderColor: '#e74a3b',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: true
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