<?php if (isset($_SESSION['usuario_id'])): ?>
    </div>
    <footer class="sticky-footer bg-white">
        <div class="container my-auto">
            <div class="copyright text-center my-auto">
                <span>CRM By Innovacode Tech &copy; <?php echo date('Y'); ?></span>
            </div>
        </div>
    </footer>
    </div>
    </div>
<?php else: ?>
    </main>
<?php endif; ?>

<?php if (isset($_SESSION['usuario_id'])): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/StartBootstrap/startbootstrap-sb-admin-2@gh-pages/js/sb-admin-2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo BASE_URL; ?>/public/js/Chart.min.js"></script>
<?php else: ?>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo BASE_URL; ?>/public/js/Chart.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/public/js/sb-admin-scripts.js"></script>
<?php endif; ?>

<script>
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

    $(function() {
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
    });
</script>

<?php if (isset($_SESSION['usuario_id'])): ?>
    <script>
        (function pollNotificaciones() {
            setTimeout(function() {
                $.getJSON('<?php echo url('notificacion/conteo'); ?>', function(res) {
                    var badge = $('#campanaBadge');
                    if (res.count > 0) {
                        badge.text(res.count > 9 ? '9+' : res.count).css('display', 'inline-flex');
                    } else {
                        badge.hide();
                    }
                }).always(function() {
                    pollNotificaciones();
                });
            }, 60000);
        })();
    </script>
<?php endif; ?>

</body>

</html>