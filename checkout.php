<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$cart_count = get_cart_count();

if ($cart_count == 0) {
    redirect('cart.php');
}

$pdo = get_db_connection();

// User info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Cart details
$cart_items = $_SESSION['cart'] ?? [];
$ids = implode(',', array_map('intval', array_keys($cart_items)));

$cart_products = [];
$total_amount = 0;

if ($ids) {
    $sql = "SELECT * FROM products WHERE id IN ($ids)";
    $cart_products = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cart_products as $p) {
        $qty = $cart_items[$p['id']];
        $total_amount += $p['price'] * $qty;
    }
}

// Handle Order Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Create Order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
        $stmt->execute([$user_id, $total_amount]);
        $order_id = $pdo->lastInsertId();

        // Create Order Items
        $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

        foreach ($cart_products as $p) {
            $qty = $cart_items[$p['id']];
            $stmt_item->execute([$order_id, $p['id'], $qty, $p['price']]);
        }

        // Clear Cart
        unset($_SESSION['cart']);

        $pdo->commit();
        redirect('order_success.php');

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error al procesar el pedido: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Finalizar Compra - FreshMarket</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap"
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
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col text-[#0d121b] dark:text-white">

    <header
        class="sticky top-0 z-50 bg-background-light dark:bg-background-dark border-b border-gray-200 dark:border-gray-800">
        <div class="flex items-center p-4 pb-2 justify-between max-w-md mx-auto">
            <a href="cart.php"
                class="text-primary cursor-pointer flex size-10 items-center justify-center rounded-full hover:bg-primary/10 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <h2 class="text-lg font-bold leading-tight flex-1 text-center pr-10">Confirmar Pedido</h2>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto max-w-md mx-auto w-full pb-32 p-4">

        <!-- Address -->
        <section class="mb-6">
            <h3 class="text-lg font-bold mb-3">Dirección de Entrega</h3>
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 p-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary mt-1">location_on</span>
                    <div class="flex-1">
                        <p class="font-bold">Domicilio</p>
                        <?php if (!empty($user['address'])): ?>
                            <p class="text-gray-500 text-sm">
                                <?= htmlspecialchars($user['address']) ?>
                            </p>
                            <p class="text-gray-500 text-sm mt-1">Tel:
                                <?= htmlspecialchars($user['phone'] ?? 'No registrado') ?>
                            </p>
                        <?php else: ?>
                            <p class="text-red-500 text-sm">No tienes dirección registrada.</p>
                            <a href="profile.php" class="text-primary text-sm font-bold mt-2 inline-block">Agregar
                                Dirección</a>
                        <?php endif; ?>
                    </div>
                    <a href="profile.php"
                        class="text-sm bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-lg font-medium">Editar</a>
                </div>
            </div>
        </section>

        <!-- Payment (Static for now) -->
        <section class="mb-6">
            <h3 class="text-lg font-bold mb-3">Método de Pago</h3>
            <div
                class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="bg-gray-100 dark:bg-gray-800 p-2 rounded-lg">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <div>
                        <p class="font-bold text-sm">Efectivo</p>
                        <p class="text-xs text-gray-500">Contra entrega</p>
                    </div>
                </div>
                <div class="size-5 rounded-full border-2 border-primary flex items-center justify-center">
                    <div class="size-2.5 bg-primary rounded-full"></div>
                </div>
            </div>
        </section>

        <!-- Summary -->
        <section class="mb-6">
            <h3 class="text-lg font-bold mb-3">Resumen</h3>
            <div class="bg-white dark:bg-gray-900 rounded-xl p-4 border border-gray-100 dark:border-gray-800 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Productos (
                        <?= array_sum($cart_items) ?>)
                    </span>
                    <span class="font-medium">$
                        <?= number_format($total_amount, 2) ?>
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Envío</span>
                    <span class="text-green-600 font-medium">Gratis</span>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-800 pt-2 mt-2 flex justify-between">
                    <span class="font-bold">Total</span>
                    <span class="font-bold text-xl text-primary">$
                        <?= number_format($total_amount, 2) ?>
                    </span>
                </div>
            </div>
        </section>

    </main>

    <footer
        class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 px-4 pt-4 pb-6 z-[60]">
        <div class="max-w-md mx-auto flex items-center justify-between gap-4">
            <div class="flex flex-col">
                <span class="text-[10px] uppercase tracking-wider text-gray-500 font-bold">Total a pagar</span>
                <span class="text-2xl font-bold leading-none">$
                    <?= number_format($total_amount, 2) ?>
                </span>
            </div>

            <form method="POST" class="flex-1">
                <?php if (empty($user['address'])): ?>
                    <button type="button" onclick="alert('Por favor agrega una dirección de entrega en tu perfil.')"
                        class="w-full bg-gray-300 text-gray-500 h-14 rounded-xl font-bold text-lg flex items-center justify-center cursor-not-allowed">
                        Falta Dirección
                    </button>
                <?php else: ?>
                    <button type="submit"
                        class="w-full bg-primary text-white h-14 rounded-xl font-bold text-lg shadow-lg shadow-primary/20 flex items-center justify-center gap-2 active:scale-[0.98] transition-transform">
                        <span>Confirmar Pedido</span>
                        <span class="material-symbols-outlined">check</span>
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </footer>

</body>

</html>