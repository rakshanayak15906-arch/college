<?php
require_once __DIR__ . '/db.php';

if (!empty($_SESSION['admin'])) {
    redirect('admin_registered.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="logo">Admin Portal</div>
    <nav>
      <a href="index.html">Home</a>
      <a href="about.html">About</a>
      <a href="courses.php">Courses</a>
      <a href="contact.html">Contact</a>
      <a href="login.html">Student Login</a>
    </nav>
  </header>

  <main class="page-content">
    <section class="section">
      <h2>Admin Login</h2>
      <form method="POST" action="admin_auth.php">
        <input type="text" name="username" placeholder="Admin username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="form-button">Login as Admin</button>
      </form>
    </section>
  </main>

  <footer>
    <p>&copy; 2026 Dr TMA Pai Polytechnic Manipal. All rights reserved.</p>
  </footer>
</body>
</html>
