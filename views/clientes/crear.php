<div class="row">
    <div class="col-12">
        <h1>Agregar Cliente</h1>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?controller=cliente&action=guardar">
            <div class="form-group">
                <label for="nombre">Nombre *</label>
                <input required type="text" class="form-control"
                    name="nombre" id="nombre"
                    placeholder="Nombre completo del cliente">
            </div>

            <div class="form-group">
                <label for="edad">Edad *</label>
                <input required type="number" class="form-control"
                    name="edad" id="edad" min="1" max="120"
                    placeholder="Edad del cliente">
            </div>

            <div class="form-group">
                <label for="departamento">Departamento *</label>
                <select required class="form-control" name="departamento" id="departamento">
                    <option value="">Seleccione un departamento</option>
                    <?php foreach ($departamentos as $depto): ?>
                        <option value="<?php echo htmlspecialchars($depto); ?>">
                            <?php echo htmlspecialchars($depto); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success">
                <span class="mdi mdi-content-save"></span> Guardar
            </button>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=cliente&action=index" class="btn btn-info">
                <span class="mdi mdi-arrow-left"></span> Volver
            </a>
        </form>
    </div>
</div>