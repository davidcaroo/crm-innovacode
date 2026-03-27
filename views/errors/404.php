<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página no encontrada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        body {
            background-color: #f3f4f6;
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
            max-width: 500px;
        }

        .error-code {
            font-size: 8rem;
            font-weight: 800;
            color: #3b82f6;
            margin-bottom: 0;
            line-height: 1;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
        }

        .error-text {
            color: #6b7280;
            margin-bottom: 2rem;
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
        <h1 class="error-code">404</h1>
        <h2 class="error-title">¡Vaya! Página no encontrada</h2>
        <p class="error-text">Lo sentimos, no pudimos encontrar la página que estás buscando. Puede que haya sido movida o eliminada.</p>
        <a href="<?php echo BASE_URL; ?>/index.php" class="btn-home">
            <i class="fa-solid fa-gauge-high"></i> Volver al Dashboard
        </a>
    </div>
</body>

</html>