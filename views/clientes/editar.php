<div class="row">
    <div class="col-12">
        <h1>Editar Cliente</h1>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form method="POST" action="<?php echo BASE_URL; ?>/index.php?controller=cliente&action=actualizar">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($cliente->id); ?>">

            <div class="form-group">
                <label for="nombre">Nombre *</label>
                <input required type="text" class="form-control"
                    name="nombre" id="nombre"
                    value="<?php echo htmlspecialchars($cliente->nombre); ?>">
            </div>

            <div class="form-group">
                <label for="edad">Edad *</label>
                <input required type="number" class="form-control"
                    name="edad" id="edad" min="1" max="120"
                    value="<?php echo htmlspecialchars($cliente->edad); ?>">
            </div>

            <div class="form-group">
                <label for="departamento">Departamento *</label>
                <select required class="form-control" name="departamento" id="departamento">
                    <?php foreach ($departamentos as $depto): ?>
                        <option value="<?php echo htmlspecialchars($depto); ?>"
                            <?php echo ($cliente->departamento === $depto) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($depto); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success">
                <span class="mdi mdi-content-save"></span> Actualizar
            </button>
            <a href="<?php echo BASE_URL; ?>/index.php?controller=cliente&action=index" class="btn btn-info">
                <span class="mdi mdi-arrow-left"></span> Cancelar
            </a>
        </form>
    </div>
</div>