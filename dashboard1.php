<?php
require_once __DIR__ . '/db.php';

if (empty($_SESSION['student_id'])) {
    redirect('login.html');
}

$student = getStudentById((int) $_SESSION['student_id']);
if (!$student) {
    session_destroy();
    redirect('login.html');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="logo">Dr TMA Pai Polytechnic</div>
    <nav>
      <a href="index.html">Home</a>
      <a href="about.html">About</a>
      <a href="courses.php">Courses</a>
      <a href="profile.php">Profile</a>
      <a href="contact.html">Contact</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main class="page-content">
    <section class="section">
      <h2>Welcome, <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
      <div class="cards">
        <div class="card">
          <h3>Student Details</h3>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
          <p><strong>Mobile:</strong> <?php echo htmlspecialchars($student['mobile']); ?></p>
          <p><strong>Course:</strong> <?php echo htmlspecialchars($student['course_name'] ?? 'Not assigned'); ?></p>
        </div>
        <div class="card">
          <h3>Enrolled Program</h3>
          <p><strong>Course Code:</strong> <?php echo htmlspecialchars($student['course_code'] ?? '-'); ?></p>
          <p><strong>Registered on:</strong> <?php echo htmlspecialchars($student['created_at']); ?></p>
        </div>
      </div>
    </section>

    <section class="section section-alt">
      <h2>Available Courses</h2>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Course</th>
              <th>Code</th>
              <th>Duration</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (getCourses() as $course): ?>
            <tr>
              <td><?php echo htmlspecialchars($course['name']); ?></td>
              <td><?php echo htmlspecialchars($course['code']); ?></td>
              <td><?php echo htmlspecialchars($course['duration']); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="section">
      <a href="profile.php" class="btn">View Profile</a>
    </section>
  </main>

  <footer>
    <p>© 2026 Dr TMA Pai Polytechnic Manipal. All rights reserved.</p>
  </footer>
</body>
</html>
