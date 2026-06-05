<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcademiSys — Iniciar Sesión</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #1a237e 0%, #283593 60%, #3949ab 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .25);
            padding: 48px 40px 40px;
            width: 100%;
            max-width: 420px;
        }

        .logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo h1 {
            font-size: 2rem;
            font-weight: 700;
            color: #1a237e;
            letter-spacing: 1px;
        }

        .logo p {
            font-size: .88rem;
            color: #78909c;
            margin-top: 4px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: .85rem;
            font-weight: 600;
            color: #455a64;
            margin-bottom: 6px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #cfd8dc;
            border-radius: 6px;
            font-size: .95rem;
            color: #37474f;
            transition: border-color .2s;
            outline: none;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #3949ab;
        }

        button[type="submit"] {
            width: 100%;
            padding: 13px;
            background: #1a237e;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
            transition: background .2s;
        }

        button[type="submit"]:hover {
            background: #283593;
        }

        .error-msg {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef9a9a;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: .88rem;
            margin-bottom: 20px;
        }

        .footer-text {
            text-align: center;
            margin-top: 28px;
            font-size: .78rem;
            color: #b0bec5;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <h1>AcademiSys</h1>
            <p>Sistema de Gestión Académica</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/controllers/login_ctrl.php" novalidate>
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    required
                    autocomplete="username"
                    value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                >
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
            </div>
            <button type="submit">Ingresar</button>
        </form>

        <p class="footer-text">AcademiSys &copy; 2025 &mdash; Universidad Champagnat</p>
    </div>
</body>
</html>
