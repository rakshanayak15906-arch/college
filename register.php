<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('signup.html');
}

$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$mobile = trim($_POST['mobile'] ?? '');
$courseId = null;

if ($firstName === '' || $lastName === '' || $email === '' || $password === '' || $mobile === '') {
    echo '<p>All fields are required. Please go back and fill in all details.</p>';
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $email)) {
    echo '<p>Please enter a valid Gmail address ending with @gmail.com.</p>';
    exit;
}

if (!preg_match('/^[0-9]{10}$/', $mobile)) {
    echo '<p>Mobile number must be exactly 10 digits.</p>';
    exit;
}

if (
    strlen($password) < 8 ||
    !preg_match('/[a-z]/', $password) ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[0-9]/', $password) ||
    !preg_match('/[^A-Za-z0-9]/', $password)
) {
    echo '<p>Password must be at least 8 characters and include uppercase, lowercase, number, and special character.</p>';
    exit;
}

if (getStudentByEmail($email)) {
    echo '<p>This email is already registered. Please use a different email or login.</p>';
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = db()->prepare(
    'INSERT INTO students (first_name, last_name, email, password_hash, mobile, course_id, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())'
);
$stmt->execute([$firstName, $lastName, $email, $passwordHash, $mobile, $courseId]);
redirect('login.html');
