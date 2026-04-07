<?php
require_once __DIR__ . '/db.php';

if (empty($_SESSION['admin'])) {
    redirect('login.html');
}

$studentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$student = getStudentById($studentId);
$courses = getCourses();

if (!$student) {
    echo '<p>Student record not found.</p>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $mobile = trim($_POST['mobile'] ?? '');
    $courseId = (int) ($_POST['course_id'] ?? 0);
    $password = $_POST['password'] ?? '';

    if ($firstName === '' || $lastName === '' || $email === '' || $mobile === '' || $courseId <= 0) {
        echo '<p>All fields except password are required.</p>';
        exit;
    }

    $passwordHash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : null;
    updateStudent($studentId, $firstName, $lastName, $email, $mobile, $courseId, $passwordHash);
    redirect('admin.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Student Record</title>
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
      <a href="admin.php">Admin</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main class="page-content">
    <section class="section">
      <h2>Edit Student</h2>
      <form method="POST" action="admin_edit_student.php?id=<?php echo (int) $studentId; ?>">
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" placeholder="First name" required>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" placeholder="Last name" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" placeholder="Email address" required>
        <input type="tel" name="mobile" value="<?php echo htmlspecialchars($student['mobile']); ?>" placeholder="Mobile number" required>
        <label for="course_id">Program</label>
        <select id="course_id" name="course_id" required>
          <option value="">Select a course</option>
          <?php foreach ($courses as $course): ?>
            <option value="<?php echo (int) $course['id']; ?>" <?php echo $course['id'] === $student['course_id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($course['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <input type="password" name="password" placeholder="New password (leave blank to keep current)">
        <button type="submit" class="form-button">Save changes</button>
      </form>
    </section>
  </main>

  <footer>
    <p>© 2026 Dr TMA Pai Polytechnic Manipal. Admin panel.</p>
  </footer>
</body>
</html>
