    </main>

    <?php $ctrl = isset($_GET['controller']) ? preg_replace('/[^a-z0-9_\-]/i', '', $_GET['controller']) : ''; ?>
    <footer class="footer <?php echo $ctrl ? 'ctrl-' . $ctrl : ''; ?>">
        <span>CRM By Innovacode Tech &copy; <?php echo date('Y'); ?> | <a href="<?php echo BASE_URL; ?>/index.php?controller=soporte&action=index" style="color:#2563eb;text-decoration:none;">Ayuda y soporte</a></span>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/js/Chart.min.js"></script>

    <!-- Script para controlar el Sidebar -->
    <script>
        $(document).ready(function() {
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
                $.getJSON('<?php echo BASE_URL; ?>/index.php?controller=notificacion&action=conteo', function(res) {
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