<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Error del Servidor</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.5.95/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff5f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-card {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            border-top: 5px solid #ef4444;
        }

        .error-icon {
            font-size: 5rem;
            color: #ef4444;
            margin-bottom: 1rem;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .error-text {
            color: #6b7280;
            margin-bottom: 1rem;
        }

        .technical-details {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 8px;
            text-align: left;
            font-family: monospace;
            font-size: 0.85rem;
            margin-bottom: 2rem;
            overflow-x: auto;
            max-height: 200px;
        }

        .btn-home {
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
        }

        .btn-home:hover {
            background-color: #2563eb;
            color: white;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="error-card">
        <div class="error-icon"><span class="mdi mdi-alert-circle-outline"></span></div>
        <h2 class="error-title">Error Interno del Sistema</h2>
        <p class="error-text">Algo salió mal en nuestros servidores. Ya hemos sido notificados y estamos trabajando en ello.</p>

        <?php if (defined('DEBUG_MODE') && DEBUG_MODE && isset($exception)): ?>
            <div class="technical-details">
                <strong>Error:</strong> <?php echo htmlspecialchars($exception->getMessage()); ?><br>
                <strong>Archivo:</strong> <?php echo htmlspecialchars($exception->getFile()); ?>:<?php echo $exception->getLine(); ?>
                <hr>
                <pre><?php echo htmlspecialchars($exception->getTraceAsString()); ?></pre>
            </div>
        <?php endif; ?>

        <a href="<?php echo BASE_URL; ?>/index.php" class="btn-home">
            <span class="mdi mdi-view-dashboard"></span> Volver al Dashboard
        </a>
    </div>
</body>

</html>