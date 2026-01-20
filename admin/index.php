<?php
session_start();
require_once '../db.php';

// Simple Admin Check (For real app, use secure auth)
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT * FROM products ORDER BY id DESC";
$products = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Panel de Inventario (Admin)</title>
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
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: max(884px, 100dvh);
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-[#0d1b0d] dark:text-white antialiased">

    <!-- Top App Bar -->
    <div
        class="sticky top-0 z-50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-center p-4 justify-between max-w-md mx-auto">
            <div class="text-[#0d1b0d] dark:text-white flex size-10 items-center justify-center">
                <span class="material-symbols-outlined">menu</span>
            </div>
            <h1 class="text-[#0d1b0d] dark:text-white text-lg font-bold leading-tight tracking-tight flex-1 px-2">
                Gestión de Inventario</h1>
            <div class="flex items-center justify-end">
                <a href="products.php"
                    class="text-primary text-sm font-bold leading-normal tracking-wide bg-primary/10 px-3 py-1.5 rounded-full hover:bg-primary/20 transition-colors">
                    Add New
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <main class="max-w-md mx-auto pb-24">
        <!-- Search Bar -->
        <div class="px-4 py-4">
            <label class="flex flex-col min-w-40 h-12 w-full">
                <div class="flex w-full flex-1 items-stretch rounded-xl h-full shadow-sm">
                    <div
                        class="text-primary flex border-none bg-white dark:bg-[#1a2e1a] items-center justify-center pl-4 rounded-l-xl border-r-0">
                        <span class="material-symbols-outlined">search</span>
                    </div>
                    <input
                        class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#0d1b0d] dark:text-white focus:outline-0 focus:ring-0 border-none bg-white dark:bg-[#1a2e1a] focus:border-none h-full placeholder:text-gray-400 dark:placeholder:text-gray-500 px-4 rounded-l-none border-l-0 pl-2 text-base font-normal leading-normal"
                        placeholder="Buscar productos..." />
                </div>
            </label>
        </div>

        <!-- Inventory List -->
        <div class="flex flex-col space-y-1">
            <?php foreach ($products as $p): ?>
                <div
                    class="flex gap-4 bg-white dark:bg-[#1a2e1a] mx-4 p-3 rounded-xl border border-gray-100 dark:border-gray-800 items-center justify-between shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-lg size-[70px] border border-gray-100 dark:border-gray-700"
                            style='background-image: url("<?= $p['image_url'] ?>");'></div>
                        <div class="flex flex-1 flex-col justify-center">
                            <p class="text-[#0d1b0d] dark:text-white text-base font-bold">
                                <?= htmlspecialchars($p['name']) ?>
                            </p>
                            <p
                                class="<?= $p['stock'] > 0 ? 'text-primary' : 'text-red-500' ?> text-xs font-semibold uppercase tracking-wider">
                                <?= $p['stock'] > 0 ? 'En Stock' : 'Agotado' ?>
                            </p>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                                $
                                <?= number_format($p['price'], 2) ?>/kg •
                                <?= $p['stock'] ?> unidades
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <a href="products.php?id=<?= $p['id'] ?>"
                            class="text-gray-400 dark:text-gray-500 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                        <a href="delete_product.php?id=<?= $p['id'] ?>" onclick="return confirm('¿Seguro?')"
                            class="text-gray-400 dark:text-gray-500 hover:text-red-500 transition-colors">
                            <span class="material-symbols-outlined">delete</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Floating Action Button -->
    <div class="fixed bottom-24 right-6 z-40">
        <a href="products.php"
            class="flex items-center justify-center bg-primary text-white size-14 rounded-full shadow-lg shadow-primary/30 hover:scale-105 active:scale-95 transition-transform">
            <span class="material-symbols-outlined text-3xl">add</span>
        </a>
    </div>

    <!-- Bottom Tab Bar -->
    <nav
        class="fixed bottom-0 left-0 right-0 z-50 bg-white/95 dark:bg-[#102210]/95 backdrop-blur-md border-t border-gray-200 dark:border-gray-800">
        <div class="max-w-md mx-auto flex justify-around items-center py-3">
            <a href="index.php" class="flex flex-col items-center gap-1 text-primary">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">inventory_2</span>
                <span class="text-[10px] font-bold">Inventario</span>
            </a>
            <a href="orders.php"
                class="flex flex-col items-center gap-1 text-gray-400 dark:text-gray-500 hover:text-primary">
                <span class="material-symbols-outlined">shopping_cart</span>
                <span class="text-[10px] font-medium">Pedidos</span>
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

</body>

</html>