<?php
session_start();
require_once '../db.php';
require_once '../functions.php';

// if (!is_admin()) { redirect('../login.php'); } // Commented out for dev ease

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$product = [
    'id' => '',
    'name' => '',
    'description' => '',
    'price' => '',
    'category_id' => '',
    'stock' => '',
    'image_url' => ''
];
$is_edit = false;

// Handle GET for Edit
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $fetched = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($fetched) {
        $product = $fetched;
        $is_edit = true;
    }
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $cat = $_POST['category_id'];
    $stock = $_POST['stock'];
    $img = $_POST['image_url']; // In real app, handle file upload

    if (isset($_POST['id']) && $_POST['id']) {
        // Update
        $stmt = $pdo->prepare("UPDATE products SET name=?, price=?, category_id=?, stock=?, image_url=? WHERE id=?");
        $stmt->execute([$name, $price, $cat, $stock, $img, $_POST['id']]);
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO products (name, price, category_id, stock, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $price, $cat, $stock, $img]);
    }
    header("Location: index.php");
    exit;
}

// Fetch categories
$cats = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>
        <?= $is_edit ? 'Editar' : 'Nuevo' ?> Producto
    </title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&amp;display=swap" rel="stylesheet" />
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
            font-family: "Plus Jakarta Sans", sans-serif
        }

        body {
            min-height: max(884px, 100dvh);
        }
    </style>
</head>

<body class="bg-background-light dark:bg-background-dark text-[#0d1b0d] dark:text-[#f8fcf8] min-h-screen">
    <div class="max-w-[480px] mx-auto min-h-screen flex flex-col shadow-xl bg-background-light dark:bg-background-dark">

        <form method="POST" action="">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">

            <!-- TopAppBar -->
            <header
                class="sticky top-0 z-50 flex items-center bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md px-4 py-4 justify-between border-b border-[#cfe7cf]/30">
                <div class="flex w-12 items-center justify-start">
                    <a href="index.php" class="text-[#0d1b0d] dark:text-[#f8fcf8]">
                        <span class="material-symbols-outlined">close</span>
                    </a>
                </div>
                <h2
                    class="text-[#0d1b0d] dark:text-[#f8fcf8] text-lg font-bold leading-tight tracking-tight flex-1 text-center font-display">
                    <?= $is_edit ? 'Editar Producto' : 'Nuevo Producto' ?>
                </h2>
                <div class="flex w-12 items-center justify-end">
                    <button type="submit"
                        class="text-primary text-base font-bold leading-normal tracking-wide shrink-0">Guardar</button>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                <!-- ProfileHeader / Image Upload -->
                <section class="flex p-6 @container">
                    <div class="flex w-full flex-col gap-5 items-center">
                        <div class="flex gap-4 flex-col items-center">
                            <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-xl min-h-40 w-40 bg-[#e7f3e7] dark:bg-[#1a331a] flex items-center justify-center border-2 border-dashed border-[#cfe7cf] dark:border-[#2d4d2d]"
                                style='background-image: url("<?= $product['image_url'] ?: 'https://via.placeholder.com/150' ?>");'>
                            </div>
                        </div>

                        <label class="flex flex-col w-full px-4">
                            <p
                                class="text-[#0d1b0d] dark:text-[#f8fcf8] text-[15px] font-semibold leading-normal pb-2 ml-1">
                                URL de Imagen</p>
                            <input name="image_url"
                                class="form-input flex w-full rounded-xl text-[#0d1b0d] dark:text-[#f8fcf8] focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#cfe7cf] dark:border-[#2d4d2d] bg-white dark:bg-[#1a331a] h-14 p-[15px] text-base font-normal"
                                value="<?= htmlspecialchars($product['image_url']) ?>"
                                placeholder="https://example.com/image.jpg" />
                        </label>
                    </div>
                </section>

                <div class="px-4 space-y-2">
                    <!-- Name -->
                    <div class="flex flex-col gap-1 py-3">
                        <label class="flex flex-col w-full">
                            <p
                                class="text-[#0d1b0d] dark:text-[#f8fcf8] text-[15px] font-semibold leading-normal pb-2 ml-1">
                                Nombre del producto</p>
                            <input required name="name"
                                class="form-input flex w-full rounded-xl text-[#0d1b0d] dark:text-[#f8fcf8] focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#cfe7cf] dark:border-[#2d4d2d] bg-white dark:bg-[#1a331a] h-14 p-[15px] text-base font-normal"
                                value="<?= htmlspecialchars($product['name']) ?>" placeholder="Ej: Tomate Cherry" />
                        </label>
                    </div>

                    <!-- Category -->
                    <div class="flex flex-col gap-1 py-3">
                        <label class="flex flex-col w-full">
                            <p
                                class="text-[#0d1b0d] dark:text-[#f8fcf8] text-[15px] font-semibold leading-normal pb-2 ml-1">
                                Categoría</p>
                            <select name="category_id"
                                class="form-select flex w-full rounded-xl text-[#0d1b0d] dark:text-[#f8fcf8] focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#cfe7cf] dark:border-[#2d4d2d] bg-white dark:bg-[#1a331a] h-14 p-[15px] text-base font-normal">
                                <?php foreach ($cats as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>

                    <!-- Price & Stock -->
                    <div class="flex flex-row gap-4 py-3">
                        <label class="flex flex-col flex-1">
                            <p
                                class="text-[#0d1b0d] dark:text-[#f8fcf8] text-[15px] font-semibold leading-normal pb-2 ml-1">
                                Precio ($)</p>
                            <input required name="price"
                                class="form-input flex w-full rounded-xl text-[#0d1b0d] dark:text-[#f8fcf8] focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#cfe7cf] dark:border-[#2d4d2d] bg-white dark:bg-[#1a331a] h-14 p-[15px] text-base font-normal"
                                value="<?= htmlspecialchars($product['price']) ?>" placeholder="0.00" step="0.01"
                                type="number" />
                        </label>
                        <label class="flex flex-col flex-1">
                            <p
                                class="text-[#0d1b0d] dark:text-[#f8fcf8] text-[15px] font-semibold leading-normal pb-2 ml-1">
                                Stock</p>
                            <input required name="stock"
                                class="form-input flex w-full rounded-xl text-[#0d1b0d] dark:text-[#f8fcf8] focus:outline-0 focus:ring-2 focus:ring-primary/50 border border-[#cfe7cf] dark:border-[#2d4d2d] bg-white dark:bg-[#1a331a] h-14 p-[15px] text-base font-normal"
                                value="<?= htmlspecialchars($product['stock']) ?>" placeholder="0" type="number" />
                        </label>
                    </div>

                    <?php if ($is_edit): ?>
                        <div class="pt-8 pb-12">
                            <a href="delete_product.php?id=<?= $product['id'] ?>"
                                onclick="return confirm('¿Eliminar este producto?')"
                                class="w-full flex items-center justify-center gap-2 text-red-500 font-semibold py-4 border border-red-100 dark:border-red-900/30 rounded-xl bg-red-50 dark:bg-red-900/10 active:opacity-70 transition-opacity">
                                <span class="material-symbols-outlined text-xl">delete</span>
                                Eliminar Producto
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </form>

        <div class="h-8 bg-background-light dark:bg-background-dark"></div>
    </div>
</body>

</html>