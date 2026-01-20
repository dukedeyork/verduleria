<?php
session_start();
require_once '../db.php';

// Auth check
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$order_id = $_GET['id'] ?? null;
if (!$order_id) {
    header('Location: orders.php');
    exit;
}

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);
    // Refresh to show new status
    header("Location: order_details.php?id=$order_id");
    exit;
}

// Fetch Order Info with User Details
$stmt = $pdo->prepare("
    SELECT o.*, u.name as user_name, u.dni, u.phone, u.address 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Pedido no encontrado");
}

// Fetch Order Items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name as product_name, p.image_url 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Detalle Pedido #
        <?= $order_id ?>
    </title>
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
                <a href="orders.php" class="text-[#0d1b0d] dark:text-white flex size-10 items-center justify-center">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <h1 class="text-[#0d1b0d] dark:text-white text-lg font-bold leading-tight tracking-tight flex-1">Pedido
                    #
                    <?= $order_id ?>
                </h1>
            </div>
        </div>

        <main class="flex-1 p-4 pb-24 space-y-6">

            <!-- Status Management -->
            <div
                class="bg-white dark:bg-[#1a2e1a] p-4 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Estado del Pedido</h2>
                <form method="POST" class="flex flex-col gap-3">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 rounded-lg text-lg font-bold capitalize">
                            <?= $order['status'] ?>
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 mt-2">
                        <button type="submit" name="status" value="pending"
                            class="px-3 py-2 rounded-lg text-xs font-bold border border-yellow-500 text-yellow-600 hover:bg-yellow-50 <?= $order['status'] == 'pending' ? 'bg-yellow-50 ring-2 ring-yellow-500' : '' ?>">
                            Pendiente
                        </button>
                        <button type="submit" name="status" value="processing"
                            class="px-3 py-2 rounded-lg text-xs font-bold border border-blue-500 text-blue-600 hover:bg-blue-50 <?= $order['status'] == 'processing' ? 'bg-blue-50 ring-2 ring-blue-500' : '' ?>">
                            En Proceso
                        </button>
                        <button type="submit" name="status" value="completed"
                            class="px-3 py-2 rounded-lg text-xs font-bold border border-green-500 text-green-600 hover:bg-green-50 <?= $order['status'] == 'completed' ? 'bg-green-50 ring-2 ring-green-500' : '' ?>">
                            Entregado
                        </button>
                    </div>
                </form>
            </div>

            <!-- Customer Info -->
            <div
                class="bg-white dark:bg-[#1a2e1a] p-4 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Cliente</h2>
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-3">
                        <div class="size-10 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">person</span>
                        </div>
                        <div>
                            <p class="font-bold">
                                <?= htmlspecialchars($order['user_name']) ?>
                            </p>
                            <p class="text-xs text-gray-500">DNI:
                                <?= htmlspecialchars($order['dni']) ?>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 mt-2 pt-2 border-t border-gray-50 dark:border-gray-800">
                        <span class="material-symbols-outlined text-gray-400 text-sm mt-1">call</span>
                        <p class="text-sm">
                            <?= htmlspecialchars($order['phone'] ?? 'Sin teléfono') ?>
                        </p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-gray-400 text-sm mt-1">location_on</span>
                        <p class="text-sm">
                            <?= htmlspecialchars($order['address'] ?? 'Sin dirección') ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div
                class="bg-white dark:bg-[#1a2e1a] p-4 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Productos</h2>
                <div class="space-y-4">
                    <?php foreach ($items as $item): ?>
                        <div class="flex gap-3">
                            <div class="size-16 bg-gray-100 rounded-lg bg-center bg-cover"
                                style="background-image: url('<?= $item['image_url'] ?>')"></div>
                            <div class="flex-1">
                                <p class="font-bold text-sm line-clamp-1">
                                    <?= htmlspecialchars($item['product_name']) ?>
                                </p>
                                <p class="text-xs text-gray-500">$
                                    <?= number_format($item['price'], 2) ?> x
                                    <?= $item['quantity'] ?> uni
                                </p>
                            </div>
                            <p class="font-bold text-sm">$
                                <?= number_format($item['price'] * $item['quantity'], 2) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>

                    <div class="border-t border-gray-100 dark:border-gray-700 pt-3 flex justify-between items-center">
                        <span class="font-bold text-lg">Total</span>
                        <span class="font-bold text-xl text-primary">$
                            <?= number_format($order['total_amount'], 2) ?>
                        </span>
                    </div>
                </div>
            </div>

        </main>

    </div>
</body>

</html>