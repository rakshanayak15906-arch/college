<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin_login.php');
}

$username = strtolower(trim($_POST['username'] ?? ''));
$password = $_POST['password'] ?? '';

if ($username === 'admin' && $password === 'root') {
    $_SESSION['admin'] = true;
    redirect('admin_registered.php');
}

echo '<p>Invalid admin credentials. <a href="admin_login.php">Try again</a>.</p>';
