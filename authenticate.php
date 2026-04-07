<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.html');
}

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    echo '<p>Please provide valid login details.</p>';
    exit;
}

if ($email === 'admin') {
    if ($password === 'root') {
        $_SESSION['admin'] = true;
        redirect('admin.php');
    }
    echo '<p>Invalid admin credentials. Please try again.</p>';
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo '<p>Please enter a valid student email address.</p>';
    exit;
}

$student = getStudentByEmail($email);
if (!$student || !password_verify($password, $student['password_hash'] ?? '')) {
    echo '<p>Invalid login credentials. Please try again.</p>';
    exit;
}

$_SESSION['student_id'] = (int) $student['id'];
redirect('dashboard.php');
