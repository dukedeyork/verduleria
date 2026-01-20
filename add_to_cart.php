<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

header('Content-Type: application/json');

if (isset($_POST['available_action']) && $_POST['available_action'] === 'add_to_cart') {
    $product_id = intval($_POST['product_id']);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if product exists in cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }

    echo json_encode(['success' => true, 'count' => array_sum($_SESSION['cart'])]);
    exit;
}

echo json_encode(['success' => false]);
?>