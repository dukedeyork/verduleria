<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $dni = trim($_POST['dni']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic Validation
    if (empty($name) || empty($dni) || empty($password) || empty($phone) || empty($address)) {
        $error = 'Por favor completa todos los campos';
    } elseif (!ctype_digit($dni) || strlen($dni) > 8) {
        $error = 'El DNI debe contener solo números y hasta 8 dígitos';
    } elseif ($password !== $confirm_password) {
        $error = 'Las contraseñas no coinciden';
    } else {
        // Check if DNI exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE dni = ?");
        $stmt->execute([$dni]);
        if ($stmt->fetch()) {
            $error = 'El DNI ya está registrado';
        } else {
            // Create user
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, dni, phone, address, password, role) VALUES (?, ?, ?, ?, ?, 'client')");
            if ($stmt->execute([$name, $dni, $phone, $address, $hashed])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['role'] = 'client';
                redirect('index.php');
            } else {
                $error = 'Error al registrar. Intenta nuevamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Registro - FreshMarket</title>
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
        class="w-full max-w-md bg-white dark:bg-[#1a2e1a] rounded-2xl shadow-xl p-8 border border-gray-100 dark:border-gray-800 my-8">
        <div class="flex flex-col items-center gap-2 mb-6">
            <div class="bg-primary/20 p-3 rounded-xl mb-2">
                <span class="material-symbols-outlined text-primary text-3xl font-bold">eco</span>
            </div>
            <h1 class="text-2xl font-bold">Crear Cuenta</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Únete a FreshMarket hoy</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-500 p-3 rounded-lg text-sm font-medium mb-4 text-center border border-red-100">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <label class="block">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Nombre Completo</span>
                <input type="text" name="name" required
                    class="form-input mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#102210] focus:border-primary focus:ring-primary"
                    placeholder="Juan Pérez">
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">DNI</span>
                <input type="text" name="dni" required pattern="[0-9]{1,8}" maxlength="8" inputmode="numeric"
                    title="Solo números, hasta 8 dígitos"
                    class="form-input mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#102210] focus:border-primary focus:ring-primary"
                    placeholder="12345678">
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Teléfono</span>
                <input type="tel" name="phone" required
                    class="form-input mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#102210] focus:border-primary focus:ring-primary"
                    placeholder="11 1234 5678">
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Dirección de Entrega</span>
                <textarea name="address" required rows="2"
                    class="form-textarea mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#102210] focus:border-primary focus:ring-primary"
                    placeholder="Calle Falsa 123, Depto 2"></textarea>
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Contraseña</span>
                <input type="password" name="password" required
                    class="form-input mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#102210] focus:border-primary focus:ring-primary"
                    placeholder="••••••••">
            </label>

            <label class="block">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Confirmar Contraseña</span>
                <input type="password" name="confirm_password" required
                    class="form-input mt-1 block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-[#102210] focus:border-primary focus:ring-primary"
                    placeholder="••••••••">
            </label>

            <button type="submit"
                class="w-full bg-primary hover:bg-green-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-primary/30 transition-all mt-4">
                Registrarse
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            ¿Ya tienes cuenta?
            <a href="login.php" class="text-primary font-bold hover:underline">Inicia Sesión</a>
        </div>

        <div class="mt-4 text-center">
            <a href="index.php" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">Volver a la
                tienda</a>
        </div>
    </div>

</body>

</html>