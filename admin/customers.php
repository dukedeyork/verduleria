<?php
session_start();
require_once '../db.php';

// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch clients
$sql = "SELECT * FROM users WHERE role = 'client' ORDER BY created_at DESC";
$clients = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Clientes - Admin</title>
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

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-[#0d1b0d] dark:text-white antialiased">
    <div class="max-w-md mx-auto min-h-screen flex flex-col shadow-xl bg-background-light dark:bg-background-dark">

        <!-- Top App Bar -->
        <div
            class="sticky top-0 z-50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800">
            <div class="flex items-center p-4 justify-between">
                <div class="text-[#0d1b0d] dark:text-white flex size-10 items-center justify-center">
                    <span class="material-symbols-outlined">group</span>
                </div>
                <h1 class="text-[#0d1b0d] dark:text-white text-lg font-bold leading-tight tracking-tight flex-1 px-2">
                    Clientes</h1>
                <div class="flex items-center justify-end">
                    <span class="text-sm font-bold text-gray-500">
                        <?= count($clients) ?> total
                    </span>
                </div>
            </div>
        </div>

        <!-- Client List -->
        <main class="flex-1 overflow-y-auto pb-24 p-4 space-y-3">
            <?php foreach ($clients as $c): ?>
                <div
                    class="flex items-start gap-4 bg-white dark:bg-[#1a2e1a] p-4 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
                    <div
                        class="flex items-center justify-center rounded-full bg-primary/10 size-12 text-primary shrink-0 mt-1">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <div class="flex flex-1 flex-col gap-1">
                        <div class="flex justify-between items-start">
                            <p class="text-[#0d1b0d] dark:text-white text-base font-bold">
                                <?= htmlspecialchars($c['name']) ?>
                            </p>
                            <a href="edit_customer.php?id=<?= $c['id'] ?>" class="text-gray-400 hover:text-primary">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </a>
                        </div>

                        <div class="flex flex-col gap-1 text-sm text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">badge</span>
                                <?= htmlspecialchars($c['dni']) ?>
                            </span>
                            <?php if (!empty($c['phone'])): ?>
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">call</span>
                                    <?= htmlspecialchars($c['phone']) ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($c['address'])): ?>
                                <span class="flex items-start gap-1">
                                    <span class="material-symbols-outlined text-sm shrink-0">location_on</span>
                                    <span class="line-clamp-2"><?= htmlspecialchars($c['address']) ?></span>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($clients)): ?>
                <div class="text-center py-10 text-gray-500">
                    <p>No hay clientes registrados aún.</p>
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
                <a href="orders.php"
                    class="flex flex-col items-center gap-1 text-gray-400 dark:text-gray-500 hover:text-primary">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    <span class="text-[10px] font-medium">Pedidos</span>
                </a>
                <a href="customers.php" class="flex flex-col items-center gap-1 text-primary">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">group</span>
                    <span class="text-[10px] font-bold">Clientes</span>
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