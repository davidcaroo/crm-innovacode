    </main>

    <?php $ctrl = isset($_GET['controller']) ? preg_replace('/[^a-z0-9_\-]/i', '', $_GET['controller']) : ''; ?>
    <footer class="footer <?php echo $ctrl ? 'ctrl-' . $ctrl : ''; ?>" style="margin-left: 0; width: 100%;">
        <span>CRM By Innovacode Tech &copy; <?php echo date('Y'); ?> | <a href="<?php echo url('soporte/index'); ?>" style="color:#2563eb;text-decoration:none;">Ayuda y soporte</a></span>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo BASE_URL; ?>/public/js/Chart.min.js"></script>

    <!-- Script para gestionar la UI y alertas -->
    <script>
        /**
         * Función global para confirmar eliminaciones con SweetAlert2
         */
        function confirmarEliminacion(url, mensaje = "¿Estás seguro de eliminar este registro?") {
            Swal.fire({
                title: mensaje,
                text: "¡Esta acción no se puede deshacer y podría eliminar datos relacionados!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
            return false;
        }

        $(document).ready(function() {
            // Manejo de notificaciones desde la URL (success / error)
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has('success')) {
                const msgType = urlParams.get('success');
                let title = "¡Éxito!";
                let text = "Operación realizada correctamente.";

                if (msgType === 'deleted') text = "Registro eliminado con éxito.";
                if (msgType === 'saved') text = "Datos guardados correctamente.";
                if (msgType === 'updated') text = "Información actualizada.";
                if (msgType === 'user_created') {
                    title = "Usuario creado";
                    text = "El usuario ha sido creado y recibirá un correo con sus credenciales.";
                }
                if (msgType === 'password_changed') {
                    title = "¡Contraseña actualizada!";
                    text = "Tu contraseña ha sido cambiada exitosamente. Bienvenido(a) al sistema.";
                }

                Swal.fire({
                    icon: 'success',
                    title: title,
                    text: text,
                    timer: 3000,
                    showConfirmButton: false
                });
            }

            if (urlParams.has('error')) {
                const errorType = urlParams.get('error');
                let title = "Error";
                let text = "Ha ocurrido un problema inesperado.";

                if (errorType === 'fk_constraint') {
                    title = "No se puede eliminar";
                    text = "Este registro tiene datos relacionados que impiden su eliminación.";
                }
                if (errorType === 'not_found') text = "El registro solicitado no existe.";
                if (errorType === 'access_denied') text = "No tienes permisos para realizar esta acción.";
                if (errorType === 'creation_failed') {
                    title = "Error al crear usuario";
                    text = "No se pudo crear el usuario en la base de datos. Contacta al administrador.";
                }

                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: text
                });
            }

            // Toggle del sidebar en móviles
            $('#sidebarToggle, #sidebarToggleMobile').on('click', function() {
                $('#sidebar').toggleClass('active');
                $('#sidebarOverlay').toggleClass('active');
            });

            // Cerrar sidebar al hacer click en el overlay
            $('#sidebarOverlay').on('click', function() {
                $('#sidebar').removeClass('active');
                $('#sidebarOverlay').removeClass('active');
            });

            // Cerrar sidebar al hacer click en un link (solo en móviles)
            if ($(window).width() <= 768) {
                $('.sidebar-link').on('click', function() {
                    $('#sidebar').removeClass('active');
                    $('#sidebarOverlay').removeClass('active');
                });
            }

            // Marcar como activo el enlace actual
            var currentUrl = window.location.href;
            $('.sidebar-link').each(function() {
                if (this.href === currentUrl) {
                    $(this).addClass('active');
                }
            });

            // Re-calcular el comportamiento del sidebar al cambiar tamaño de ventana
            var resizeTimer;
            $(window).on('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    if ($(window).width() > 768) {
                        // Desktop: remover clases activas del overlay
                        $('#sidebar').removeClass('active');
                        $('#sidebarOverlay').removeClass('active');
                    }
                }, 250);
            });
        });
    </script>
    <!-- Polling campana de notificaciones (cada 60s) -->
    <script>
        (function pollNotificaciones() {
            setTimeout(function() {
                $.getJSON('<?php echo url('notificacion/conteo'); ?>', function(res) {
                    var badge = $('#campanaBadge');
                    if (res.count > 0) {
                        badge.text(res.count > 9 ? '9+' : res.count).css('display', 'flex');
                    } else {
                        badge.hide();
                    }
                }).always(function() {
                    pollNotificaciones();
                });
            }, 60000);
        })();
    </script>
    </body>

    </html>