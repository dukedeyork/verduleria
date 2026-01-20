<?php
session_start();

function get_db_connection()
{
    // Best practice to avoid scope issues with include_once: just connect.
    $host = 'localhost';
    $dbname = 'verduleria';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function is_admin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url)
{
    header("Location: $url");
    exit;
}

// Cart Helper Functions
function get_cart_count()
{
    return isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
}

function format_currency($amount)
{
    return '$' . number_format($amount, 2);
}
?>