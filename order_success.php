<?php
session_start();
require_once 'functions.php';
// if (!is_logged_in()) redirect('login.php');
?>
<!DOCTYPE html>
<html class="light" lang="es">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Pedido Confirmado</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body
    class="bg-gray-50 dark:bg-[#101622] text-gray-900 dark:text-white h-screen flex flex-col items-center justify-center p-6 text-center">

    <div class="bg-green-100 text-green-600 rounded-full p-6 mb-6">
        <span class="material-symbols-outlined text-6xl">check_circle</span>
    </div>

    <h1 class="text-3xl font-bold mb-2">¡Pedido Recibido!</h1>
    <p class="text-gray-500 mb-8 max-w-xs mx-auto">Tu pedido ha sido procesado exitosamente. Te contactaremos pronto
        para la entrega.</p>

    <a href="index.php"
        class="w-full max-w-xs bg-[#13ec13] text-white py-4 rounded-xl font-bold shadow-lg shadow-green-500/30 hover:bg-green-600 transition-colors">
        Volver a la Tienda
    </a>

</body>

</html>