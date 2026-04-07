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

$applications = getApplicationsByStudent((int) $student['id'], (string) $student['email']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile</title>
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
      <a href="dashboard.php">Dashboard</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main class="page-content">
    <section class="section">
      <h2>My Profile</h2>
      <div class="cards">
        <div class="card">
          <h3>Personal Details</h3>
          <p><strong>Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></p>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
          <p><strong>Mobile:</strong> <?php echo htmlspecialchars($student['mobile']); ?></p>
          <p><strong>Registered On:</strong> <?php echo htmlspecialchars($student['created_at']); ?></p>
        </div>
        <div class="card">
          <h3>Current Program</h3>
          <p><strong>Course:</strong> <?php echo htmlspecialchars($student['course_name'] ?? 'Not assigned'); ?></p>
          <p><strong>Course Code:</strong> <?php echo htmlspecialchars($student['course_code'] ?? '-'); ?></p>
          <p><strong>Status:</strong> Active student account</p>
        </div>
      </div>
    </section>

    <section class="section section-alt">
      <h2>Applied Courses</h2>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Applied On</th>
              <th>Course</th>
              <th>Code</th>
              <th>Duration</th>
              <th>Qualification</th>
              <th>Marks</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($applications) === 0): ?>
              <tr>
                <td colspan="7" style="text-align:center; padding:24px; color:#475569;">No course applications found yet.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($applications as $application): ?>
                <tr>
                  <td><?php echo htmlspecialchars($application['created_at']); ?></td>
                  <td><?php echo htmlspecialchars($application['course_name']); ?></td>
                  <td><?php echo htmlspecialchars($application['course_code']); ?></td>
                  <td><?php echo htmlspecialchars($application['course_duration']); ?></td>
                  <td><?php echo htmlspecialchars($application['qualification']); ?></td>
                  <td><?php echo htmlspecialchars($application['marks']); ?></td>
                  <td>Submitted</td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <p style="margin-top:16px; color:#475569;">Want to apply for another program? <a href="courses.php">Browse courses</a></p>
    </section>
  </main>

  <footer>
    <p>&copy; 2026 Dr TMA Pai Polytechnic Manipal. All rights reserved.</p>
  </footer>
</body>
</html>
