<?php
require_once 'functions.php';

$pdo = get_db_connection();

// Filter by category if set
$category_id = isset($_GET['category']) ? $_GET['category'] : null;
$where = "";
$params = [];
if ($category_id && $category_id !== 'all') {
    $where = "WHERE category_id = ?";
    $params = [$category_id];
}

// Fetch products
$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id $where";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Fetch categories for filter
$cats = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Tienda de Frutas y Verduras</title>
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
                    colors: {
                        "primary": "#13ec13",
                        "background-light": "#f6f8f6",
                        "background-dark": "#102210",
                    },
                    fontFamily: {
                        "display": ["Plus Jakarta Sans"]
                    },
                    borderRadius: { "DEFAULT": "0.5rem", "lg": "1rem", "xl": "1.5rem", "full": "9999px" },
                },
            },
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .fill-icon {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        body {
            min-height: max(884px, 100dvh);
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-[#0d1b0d] dark:text-white min-h-screen flex flex-col">

    <!-- TopAppBar -->
    <nav class="sticky top-0 z-50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md px-4 pt-6 pb-2">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="bg-primary/20 p-2 rounded-lg">
                    <span class="material-symbols-outlined text-primary font-bold">eco</span>
                </div>
                <h2 class="text-xl font-bold leading-tight tracking-tight">FreshMarket</h2>
            </div>

            <!-- Cart Icon -->
            <div class="relative">
                <a href="cart.php"
                    class="flex items-center justify-center rounded-full bg-white dark:bg-zinc-800 shadow-sm border border-zinc-200 dark:border-zinc-700 w-10 h-10">
                    <span class="material-symbols-outlined text-zinc-700 dark:text-zinc-200">shopping_cart</span>
                </a>
                <?php if (get_cart_count() > 0): ?>
                    <span id="cart-badge"
                        class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] font-bold text-white border-2 border-background-light dark:border-background-dark">
                        <?= get_cart_count() ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- SearchBar -->
        <div class="mt-4">
            <form action="" method="GET">
                <label class="flex flex-col w-full">
                    <div
                        class="flex w-full items-stretch rounded-xl h-12 bg-white dark:bg-zinc-800 shadow-sm border border-zinc-100 dark:border-zinc-700">
                        <div class="flex items-center justify-center pl-4 text-zinc-400">
                            <span class="material-symbols-outlined">search</span>
                        </div>
                        <!-- Simple search not implemented in DB query yet but frontend is ready -->
                        <input name="q"
                            class="form-input flex w-full min-w-0 flex-1 border-none bg-transparent focus:ring-0 text-base font-normal placeholder:text-zinc-400"
                            placeholder="Buscar frutas y verduras..." />
                    </div>
                </label>
            </form>
        </div>
    </nav>

    <!-- Categories -->
    <div class="flex gap-3 px-4 py-4 overflow-x-auto no-scrollbar">
        <a href="index.php?category=all"
            class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full <?= !$category_id || $category_id === 'all' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-100 dark:border-zinc-700' ?> px-6">
            <p class="text-sm font-semibold">Todos</p>
        </a>
        <?php foreach ($cats as $cat): ?>
            <a href="index.php?category=<?= $cat['id'] ?>"
                class="flex h-10 shrink-0 items-center justify-center gap-x-2 rounded-full <?= $category_id == $cat['id'] ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-white dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-100 dark:border-zinc-700' ?> px-6">
                <p class="text-sm font-semibold">
                    <?= htmlspecialchars($cat['name']) ?>
                </p>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Product Section Title -->
    <div class="px-4 flex items-center justify-between">
        <h3 class="text-lg font-bold">Ofertas de hoy</h3>
        <button class="text-primary text-sm font-semibold">Ver todo</button>
    </div>

    <!-- ImageGrid (Product Grid) -->
    <main class="flex-1 overflow-y-auto pb-24">
        <div class="grid grid-cols-2 gap-4 p-4">
            <?php foreach ($products as $product): ?>
                <div
                    class="flex flex-col gap-3 bg-white dark:bg-zinc-800 p-3 rounded-xl shadow-sm border border-zinc-50 dark:border-zinc-700 relative group">
                    <div class="w-full bg-center bg-no-repeat aspect-square bg-cover rounded-lg overflow-hidden relative"
                        style='background-image: url("<?= $product['image_url'] ?>");'>
                    </div>
                    <div class="flex flex-col gap-1">
                        <p class="text-[#0d1b0d] dark:text-zinc-100 text-base font-bold leading-tight">
                            <?= htmlspecialchars($product['name']) ?>
                        </p>
                        <p class="text-zinc-500 dark:text-zinc-400 text-sm font-medium">$
                            <?= number_format($product['price'], 2) ?> / unit
                        </p>
                    </div>
                    <button onclick="addToCart(<?= $product['id'] ?>)"
                        class="absolute bottom-3 right-3 flex size-10 items-center justify-center rounded-full bg-primary text-white shadow-md active:scale-95 transition-transform hover:bg-green-600">
                        <span class="material-symbols-outlined font-bold">add</span>
                    </button>
                </div>
            <?php endforeach; ?>

            <?php if (empty($products)): ?>
                <div class="col-span-2 text-center py-10 text-zinc-500">
                    <p>No hay productos disponibles.</p>
                    <p class="text-sm">Ejecuta php seed.php para cargar datos de prueba.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- BottomNavBar -->
    <footer
        class="fixed bottom-0 left-0 right-0 bg-white/90 dark:bg-background-dark/90 backdrop-blur-lg border-t border-zinc-200 dark:border-zinc-800 px-4 pb-8 pt-2">
        <div class="flex justify-around items-center max-w-md mx-auto">
            <a class="flex flex-col items-center gap-1 text-primary" href="index.php">
                <span class="material-symbols-outlined fill-icon text-[28px]">home</span>
                <p class="text-[10px] font-bold tracking-wide uppercase">Inicio</p>
            </a>
            <a class="flex flex-col items-center gap-1 text-zinc-400 dark:text-zinc-500" href="#">
                <span class="material-symbols-outlined text-[28px]">search</span>
                <p class="text-[10px] font-medium tracking-wide uppercase">Buscar</p>
            </a>
            <a class="flex flex-col items-center gap-1 text-zinc-400 dark:text-zinc-500" href="cart.php">
                <span class="material-symbols-outlined text-[28px]">receipt_long</span>
                <p class="text-[10px] font-medium tracking-wide uppercase">Pedidos</p>
            </a>
            <?php if (is_logged_in()): ?>
                <a class="flex flex-col items-center gap-1 text-red-500" href="logout.php">
                    <span class="material-symbols-outlined text-[28px]">logout</span>
                    <p class="text-[10px] font-medium tracking-wide uppercase">Salir</p>
                </a>
            <?php else: ?>
                <a class="flex flex-col items-center gap-1 text-zinc-400 dark:text-zinc-500" href="login.php">
                    <span class="material-symbols-outlined text-[28px]">person</span>
                    <p class="text-[10px] font-medium tracking-wide uppercase">Perfil</p>
                </a>
            <?php endif; ?>
        </div>
    </footer>

    <script>
        async function addToCart(productId) {
            try {
                const formData = new FormData();
                formData.append('available_action', 'add_to_cart');
                formData.append('product_id', productId);

                const response = await fetch('add_to_cart.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    // Update badge if exists or create it
                    // For simplicity, just reload or show alert. 
                    // Ideally update DOM.
                    location.reload();
                } else {
                    alert('Error agregando al carrito');
                }
            } catch (e) {
                console.error(e);
            }
        }
    </script>

</body>

</html>