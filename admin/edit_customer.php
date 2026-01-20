<?php
session_start();
require_once '../db.php';
require_once '../functions.php';

// Check admin
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: customers.php');
    exit;
}

$error = '';
$success = '';

// Handle Post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $dni = trim($_POST['dni']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($name) || empty($dni)) {
        $error = "Nombre y DNI son obligatorios.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, dni = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->execute([$name, $dni, $phone, $address, $id]);
            $success = "Cliente actualizado correctamente.";
        } catch (PDOException $e) {
            $error = "Error al actualizar: " . $e->getMessage();
        }
    }
}

// Fetch user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    die("Cliente no encontrado.");
}
?>
<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Editar Cliente - Admin</title>
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

<body class="bg-background-light dark:bg-background-dark text-[#0d1b0d] dark:text-white antialiased">
    <div class="max-w-md mx-auto min-h-screen flex flex-col shadow-xl bg-background-light dark:bg-background-dark">

        <div
            class="sticky top-0 z-50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center p-4 gap-4">
                <a href="customers.php" class="text-[#0d1b0d] dark:text-white flex size-10 items-center justify-center">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h1 class="text-[#0d1b0d] dark:text-white text-lg font-bold leading-tight tracking-tight flex-1">Editar
                    Cliente</h1>
            </div>
        </div>

        <main class="flex-1 p-4">
            <?php if ($error): ?>
                <div class="bg-red-50 text-red-500 p-3 rounded-lg text-sm mb-4 border border-red-100">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="bg-green-50 text-green-500 p-3 rounded-lg text-sm mb-4 border border-green-100">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <label class="block">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Nombre Completo</span>
                    <div
                        class="flex items-center bg-white dark:bg-[#1a2e1a] rounded-xl border border-gray-200 dark:border-gray-700 mt-1">
                        <span class="material-symbols-outlined text-gray-400 pl-3">person</span>
                        <input type="text" name="name" value="<?= htmlspecialchars($client['name']) ?>" required
                            class="form-input border-none bg-transparent w-full focus:ring-0">
                    </div>
                </label>

                <label class="block">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">DNI</span>
                    <div
                        class="flex items-center bg-white dark:bg-[#1a2e1a] rounded-xl border border-gray-200 dark:border-gray-700 mt-1">
                        <span class="material-symbols-outlined text-gray-400 pl-3">badge</span>
                        <input type="text" name="dni" value="<?= htmlspecialchars($client['dni']) ?>" required
                            class="form-input border-none bg-transparent w-full focus:ring-0">
                    </div>
                </label>

                <label class="block">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Teléfono</span>
                    <div
                        class="flex items-center bg-white dark:bg-[#1a2e1a] rounded-xl border border-gray-200 dark:border-gray-700 mt-1">
                        <span class="material-symbols-outlined text-gray-400 pl-3">call</span>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($client['phone'] ?? '') ?>"
                            class="form-input border-none bg-transparent w-full focus:ring-0">
                    </div>
                </label>

                <label class="block">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider ml-1">Dirección de
                        Entrega</span>
                    <div
                        class="flex items-start bg-white dark:bg-[#1a2e1a] rounded-xl border border-gray-200 dark:border-gray-700 mt-1">
                        <span class="material-symbols-outlined text-gray-400 pl-3 pt-3">location_on</span>
                        <textarea name="address" rows="3"
                            class="form-textarea border-none bg-transparent w-full focus:ring-0 resize-none"><?= htmlspecialchars($client['address'] ?? '') ?></textarea>
                    </div>
                </label>

                <button type="submit"
                    class="w-full bg-primary hover:bg-green-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-primary/30 transition-all mt-6">
                    Guardar Cambios
                </button>
            </form>
        </main>

    </div>
</body>

</html>