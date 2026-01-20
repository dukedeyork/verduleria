<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dni = trim($_POST['dni']);
    $password = $_POST['password'];

    if (empty($dni) || empty($password)) {
        $error = 'Por favor completa todos los campos';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE dni = ?");
        $stmt->execute([$dni]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                redirect('admin/index.php');
            } else {
                redirect('index.php');
            }
        } else {
            $error = 'Credenciales incorrectas';
        }
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Iniciar Sesión - FreshMarket</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: { "primary": "#13ec13", "background-light": "#f6f8f6", "background-dark": "#102210" },
                    fontFamily: { "display": ["Plus Jakarta Sans", "sans-serif"] },
                    borderRadius: { "DEFAULT": "0.5rem", "lg": "1rem", "xl": "1.5rem", "full": "9999px" },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: "Plus Jakarta Sans", sans-serif;
            min-height: 100dvh;
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark text-[#0d1b0d] dark:text-white flex flex-col justify-center items-center p-4">

    <div
        class="w-full max-w-md bg-white dark:bg-[#1a2e1a] rounded-2xl shadow-xl p-8 border border-gray-100 dark:border-gray-800">
        <div class="flex flex-col items-center gap-2 mb-8">
            <div class="bg-primary/20 p-3 rounded-xl mb-2">
                <span class="material-symbols-outlined text-primary text-3xl font-bold">eco</span>
            </div>
            <h1 class="text-2xl font-bold">Bienvenido de nuevo</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Inicia sesión con tu DNI</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-500 p-3 rounded-lg text-sm font-medium mb-4 text-center border border-red-100">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-5">
            <label class="block">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">DNI</span>
                <input type="text" name="dni" required pattern="[0-9]{1,8}" maxlength="8" inputmode="numeric"
                    class="form-input mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#102210] focus:border-primary focus:ring-primary"
                    placeholder="12345678">
            </label>

            <label class="block">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Contraseña</span>
                </div>
                <input type="password" name="password" required
                    class="form-input mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#102210] focus:border-primary focus:ring-primary"
                    placeholder="••••••••">
            </label>

            <button type="submit"
                class="w-full bg-primary hover:bg-green-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-primary/30 transition-all mt-4">
                Iniciar Sesión
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-500">
            ¿No tienes cuenta?
            <a href="register.php" class="text-primary font-bold hover:underline">Regístrate</a>
        </div>

        <div class="mt-4 text-center">
            <a href="index.php" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">Volver a la
                tienda</a>
        </div>
    </div>

</body>

</html>