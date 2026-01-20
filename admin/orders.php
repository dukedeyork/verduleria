<?php
session_start();
require_once '../db.php';
require_once '../functions.php';

// Auth check
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch orders with user info
$sql = "SELECT o.*, u.name as user_name, u.dni FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
$orders = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Pedidos - Admin</title>
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

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-[#0d1b0d] dark:text-white antialiased">
    <div class="max-w-md mx-auto min-h-screen flex flex-col shadow-xl bg-background-light dark:bg-background-dark">

        <div
            class="sticky top-0 z-50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center p-4 justify-between">
                <div class="flex size-10 items-center justify-center">
                    <span class="material-symbols-outlined">shopping_cart</span>
                </div>
                <h1 class="text-lg font-bold leading-tight flex-1 px-2">Pedidos</h1>
                <span class="text-sm font-bold text-gray-500">
                    <?= count($orders) ?> nuevos
                </span>
            </div>
        </div>

        <main class="flex-1 overflow-y-auto pb-24 p-4 space-y-3">
            <?php foreach ($orders as $o): ?>
                <div
                    class="bg-white dark:bg-[#1a2e1a] p-4 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-bold text-lg">#
                                <?= $o['id'] ?> -
                                <?= htmlspecialchars($o['user_name']) ?>
                            </p>
                            <p class="text-xs text-gray-500">DNI:
                                <?= htmlspecialchars($o['dni']) ?>
                            </p>
                        </div>
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded capitalize">
                            <?= $o['status'] ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-end mt-2">
                        <p class="text-gray-400 text-xs">
                            <?= date('d/m/Y H:i', strtotime($o['created_at'])) ?>
                        </p>
                        <p class="font-bold text-primary text-xl">$
                            <?= number_format($o['total_amount'], 2) ?>
                        </p>
                    </div>
                    <div class="mt-3 border-t border-gray-100 dark:border-gray-800 pt-2 flex justify-end">
                        <a href="order_details.php?id=<?= $o['id'] ?>" class="text-sm text-primary font-bold">Ver
                            Detalles</a>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($orders)): ?>
                <div class="text-center py-10 text-gray-500">
                    <p>No hay pedidos registrados.</p>
                </div>
            <?php endif; ?>
        </main>

        <!-- Bottom Tab Bar -->
        <nav
            class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 dark:bg-[#102210]/95 backdrop-blur-md border-t border-gray-200 dark:border-gray-800">
            <div class="max-w-md mx-auto flex justify-around items-center py-3">
                <a href="index.php"
                    class="flex flex-col items-center gap-1 text-gray-400 dark:text-gray-500 hover:text-primary">
                    <span class="material-symbols-outlined">inventory_2</span>
                    <span class="text-[10px] font-medium">Inventario</span>
                </a>
                <a href="orders.php" class="flex flex-col items-center gap-1 text-primary">
                    <span class="material-symbols-outlined"
                        style="font-variation-settings: 'FILL' 1;">shopping_cart</span>
                    <span class="text-[10px] font-bold">Pedidos</span>
                </a>
                <a href="customers.php"
                    class="flex flex-col items-center gap-1 text-gray-400 dark:text-gray-500 hover:text-primary">
                    <span class="material-symbols-outlined">group</span>
                    <span class="text-[10px] font-medium">Clientes</span>
                </a>
                <a href="../logout.php"
                    class="flex flex-col items-center gap-1 text-gray-400 dark:text-gray-500 hover:text-red-500">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="text-[10px] font-medium">Salir</span>
                </a>
            </div>
            <div class="h-5"></div>
        </nav>

    </div>
</body>

</html>