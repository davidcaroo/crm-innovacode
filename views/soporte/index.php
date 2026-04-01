<?php
// views/soporte/index.php
?>
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-life-ring mr-2 text-primary"></i> Centro de Ayuda & Soporte</h1>
</div>

<div class="row">
    <!-- Soporte Inmediato -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-headset mr-2"></i>Contacto Rápido</h6>
            </div>
            <div class="card-body text-center">
                <div class="icon-circle bg-success text-white mx-auto mt-2 mb-4" style="width: 70px; height: 70px; font-size: 2rem;">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <p class="text-gray-600 mb-4">Si experimentas un fallo crítico o necesitas asistencia prioritaria, comunícate directamente con nuestro equipo de soporte técnico.</p>
                <a target="_blank" href="https://wa.me/573116061807?text=Hola%20David%2C%20necesito%20soporte%20t%C3%A9cnico%20con%20el%20CRM" class="btn btn-success btn-block shadow-sm">
                    <i class="fab fa-whatsapp mr-2"></i> Escribir a Soporte
                </a>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-2"></i>Información del Sistema</h6>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center border-top-0">
                        Versión instalada
                        <span class="badge badge-primary badge-pill px-2 py-1"><?php echo defined('APP_VERSION') ? APP_VERSION : '2.0.0'; ?></span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        Estado del Servidor
                        <span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i>Óptimo</span>
                    </li>
                    <li class="list-group-item px-0 border-bottom-0 d-flex justify-content-between align-items-center">
                        Licencia de uso
                        <span class="text-gray-800 font-weight-bold">Activa</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Info Proveedor -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-building mr-2"></i>Acerca del Proveedor</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4 mt-3">
                    <div class="icon-circle bg-primary text-white mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3 class="font-weight-bold text-gray-800 mb-1">InnovaCode Solutions S.A.S.</h3>
                    <p class="text-gray-500 mb-0">Innovamos tus ideas, transformamos tu negocio</p>
                </div>
                
                <hr class="mt-4 mb-4">
                
                <div class="row mt-4">
                    <div class="col-md-6 mb-4">
                        <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-check-circle mr-2"></i>Portafolio de Servicios</h6>
                        <ul class="list-unstyled pl-2 text-gray-700">
                            <li class="mb-2"><i class="fas fa-angle-right text-success mr-2"></i>Desarrollo de Software a Medida</li>
                            <li class="mb-2"><i class="fas fa-angle-right text-success mr-2"></i>Inteligencia Artificial aplicada</li>
                            <li class="mb-2"><i class="fas fa-angle-right text-success mr-2"></i>Automatización de Procesos (RPA)</li>
                            <li class="mb-2"><i class="fas fa-angle-right text-success mr-2"></i>Transformación Digital Empresarial</li>
                        </ul>
                    </div>
                    <div class="col-md-6 mb-4 border-left d-none d-md-block">
                        <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-user-circle mr-2"></i>Ing. David Caro</h6>
                        <p class="small text-gray-600 mb-3">Ingeniero Informático & Desarrollador</p>
                        <div class="text-gray-700">
                            <div class="mb-2"><i class="fas fa-envelope text-primary mr-2" style="width:16px;"></i> dacamo0502@gmail.com</div>
                            <div class="mb-2"><i class="fas fa-map-marker-alt text-danger mr-2" style="width:16px;"></i> Cartagena de Indias, Colombia</div>
                            <div class="mt-3">
                                <a href="https://innovacode.click/" target="_blank" class="btn btn-sm btn-outline-primary shadow-sm mr-2 mb-1">
                                    <i class="fas fa-globe mr-1"></i>Portfolio Web
                                </a>
                                <a href="https://linkedin.com/in/ingdavid-caro" target="_blank" class="btn btn-sm btn-outline-info shadow-sm mb-1">
                                    <i class="fab fa-linkedin mr-1"></i>LinkedIn
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-left-info shadow-sm mt-3 mb-1">
                    <h6 class="font-weight-bold text-info border-bottom-info pb-2 mb-2">¿Necesitas una funcionalidad nueva en tu CRM?</h6>
                    <p class="mb-2 text-gray-700">Nuestro equipo está listo para desarrollar e integrar módulos personalizados. Te ayudamos a escalar la operación de tu empresa.</p>
                    <a href="https://innovacode.click/" target="_blank" class="alert-link font-weight-bold text-primary">Solicitar auditoría tecnológica o cotización &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>