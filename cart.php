<?php
require_once 'functions.php';
$pdo = get_db_connection();

// Create a list of product IDs from the session cart
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products_in_cart = [];
$total_price = 0;

if (!empty($cart_items)) {
    // Sanitize IDs
    $ids = implode(',', array_map('intval', array_keys($cart_items)));
    if ($ids) {
        $sql = "SELECT * FROM products WHERE id IN ($ids)";
        $stmt = $pdo->query($sql);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $p) {
            $qty = $cart_items[$p['id']];
            $p['qty'] = $qty;
            $p['subtotal'] = $p['price'] * $qty;
            $total_price += $p['subtotal'];
            $products_in_cart[] = $p;
        }
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Carrito de Compras</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#13ec13",
                        "background-light": "#f6f8f6",
                        "background-dark": "#102210",
                    },
                    fontFamily: {
                        "display": ["Public Sans"]
                    },
                    borderRadius: { "DEFAULT": "0.5rem", "lg": "1rem", "xl": "1.5rem", "full": "9999px" },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        body {
            min-height: max(884px, 100dvh);
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-[#0d1b0d] dark:text-white antialiased">
    <div
        class="flex flex-col h-screen max-w-md mx-auto relative overflow-hidden shadow-2xl bg-white dark:bg-background-dark">

        <!-- TopAppBar -->
        <header
            class="sticky top-0 z-20 bg-white/80 dark:bg-background-dark/80 backdrop-blur-md px-4 pt-10 pb-4 border-b border-gray-100 dark:border-white/10">
            <div class="flex items-center justify-between">
                <a href="index.php"
                    class="flex size-10 items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-white/10">
                    <span class="material-symbols-outlined">arrow_back_ios_new</span>
                </a>
                <h2 class="text-lg font-bold leading-tight tracking-tight text-center">Mi Carrito</h2>
                <div class="size-10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary">shopping_basket</span>
                </div>
            </div>
        </header>

        <!-- Main Content (Scrollable Area) -->
        <main class="flex-1 overflow-y-auto no-scrollbar pb-40">
            <div class="p-4 space-y-4">

                <?php if (empty($products_in_cart)): ?>
                    <div class="text-center py-10 opacity-75">
                        <p>Tu carrito está vacío.</p>
                        <a href="index.php" class="text-primary font-bold mt-2 inline-block">Ir a comprar</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($products_in_cart as $item): ?>
                        <!-- ListItem -->
                        <div
                            class="flex items-center gap-4 bg-white dark:bg-white/5 p-3 rounded-xl shadow-sm border border-gray-50 dark:border-white/5 transition-all">
                            <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-lg size-20 shadow-inner"
                                style='background-image: url("<?= $item['image_url'] ?>");'>
                            </div>
                            <div class="flex flex-col flex-1 justify-between py-1">
                                <div>
                                    <p class="text-base font-bold leading-tight line-clamp-1">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </p>
                                    <p class="text-primary text-sm font-semibold mt-1">$
                                        <?= number_format($item['price'], 2) ?>
                                    </p>
                                </div>
                                <div class="flex items-center justify-end mt-2">
                                    <div class="flex items-center gap-3 bg-background-light dark:bg-white/10 p-1 rounded-full">
                                        <!-- TODO: Implement remove functionality -->
                                        <button
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-white dark:bg-background-dark shadow-sm hover:scale-105 active:scale-95 transition-transform">
                                            <span class="material-symbols-outlined text-sm">remove</span>
                                        </button>
                                        <span class="text-base font-bold w-6 text-center">
                                            <?= $item['qty'] ?>
                                        </span>
                                        <!-- TODO: Implement add functionality -->
                                        <button
                                            class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white shadow-md shadow-primary/30 hover:scale-105 active:scale-95 transition-transform">
                                            <span class="material-symbols-outlined text-sm">add</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
        </main>

        <!-- Sticky Footer Checkout Section -->
        <section
            class="absolute bottom-0 left-0 right-0 z-30 bg-white dark:bg-background-dark border-t border-gray-100 dark:border-white/10 p-4 pt-6 pb-24 shadow-[0_-10px_20px_-5px_rgba(0,0,0,0.05)]">
            <!-- Summary List -->
            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center">
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Subtotal</p>
                    <p class="text-sm font-bold">$
                        <?= number_format($total_price, 2) ?>
                    </p>
                </div>
                <div class="flex justify-between items-center">
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Costo de Envío</p>
                    <p class="text-sm font-bold text-primary">GRATIS</p>
                </div>
                <div class="pt-3 border-t border-gray-100 dark:border-white/10 flex justify-between items-center">
                    <p class="text-base font-bold">Total</p>
                    <p class="text-xl font-black text-primary">$
                        <?= number_format($total_price, 2) ?>
                    </p>
                </div>
            </div>
            <!-- Checkout Button -->
            <?php if (is_logged_in()): ?>
                <a href="checkout.php"
                    class="w-full bg-primary text-white font-bold py-4 rounded-xl shadow-lg shadow-primary/30 active:scale-[0.98] transition-all flex items-center justify-center gap-2 group">
                    Finalizar Pedido
                    <span
                        class="material-symbols-outlined transition-transform group-hover:translate-x-1">arrow_forward</span>
                </a>
            <?php else: ?>
                <a href="login.php"
                    class="w-full bg-zinc-800 text-white font-bold py-4 rounded-xl shadow-lg active:scale-[0.98] transition-all flex items-center justify-center gap-2 group">
                    Ingresa para Comprar
                    <span class="material-symbols-outlined transition-transform group-hover:translate-x-1">login</span>
                </a>
            <?php endif; ?>
        </section>

        <!-- Bottom Navigation Bar -->
        <nav
            class="absolute bottom-0 left-0 right-0 bg-white/90 dark:bg-background-dark/90 backdrop-blur-lg border-t border-gray-100 dark:border-white/10 h-20 px-6 flex items-center justify-between z-40">
            <a href="index.php" class="flex flex-col items-center gap-1 text-gray-400 dark:text-gray-500">
                <span class="material-symbols-outlined text-2xl">storefront</span>
                <span class="text-[10px] font-medium">Tienda</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-1 text-primary relative">
                <span class="material-symbols-outlined text-2xl"
                    style="font-variation-settings: 'FILL' 1;">shopping_cart</span>
                <span class="text-[10px] font-bold">Carrito</span>
                <?php if (get_cart_count() > 0): ?>
                    <span
                        class="absolute -top-1 -right-1 bg-red-500 text-white text-[8px] size-4 rounded-full flex items-center justify-center border-2 border-white dark:border-background-dark">
                        <?= get_cart_count() ?>
                    </span>
                <?php endif; ?>
            </a>
            <a href="#" class="flex flex-col items-center gap-1 text-gray-400 dark:text-gray-500">
                <span class="material-symbols-outlined text-2xl">receipt_long</span>
                <span class="text-[10px] font-medium">Pedidos</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-1 text-gray-400 dark:text-gray-500">
                <span class="material-symbols-outlined text-2xl">person</span>
                <span class="text-[10px] font-medium">Perfil</span>
            </a>
        </nav>

        <!-- iPhone Notch Indicator Mockup -->
        <div
            class="absolute bottom-1 left-1/2 -translate-x-1/2 w-32 h-1 bg-black/20 dark:bg-white/20 rounded-full z-50">
        </div>
    </div>
</body>

</html>