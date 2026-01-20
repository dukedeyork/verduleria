<?php
session_start();
require_once '../db.php';
// require_once '../functions.php';
// if (!is_admin()) { redirect('../login.php'); }

if (isset($_GET['id'])) {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['id']]);
}

header("Location: index.php");
exit;
?>