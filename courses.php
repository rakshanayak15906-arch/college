<?php
require_once __DIR__ . '/db.php';
$courses = getCourses();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Courses at Dr TMA Pai Polytechnic</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="logo">Dr TMA Pai Polytechnic</div>
    <nav>
      <a href="index.html">Home</a>
      <a href="about.html">About</a>
      <a href="courses.php">Courses</a>
      <a href="contact.html">Contact</a>
      <a href="login.html">Login</a>
      <a href="signup.html">Sign Up</a>
    </nav>
  </header>

  <main class="page-content">
    <section class="section">
      <h2>Diploma Courses</h2>
      <p>Explore the currently available diploma programs. Each course includes detailed training, practical labs, and industry-focused instruction.</p>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Course</th>
              <th>Code</th>
              <th>Duration</th>
              <th>Description</th>
              <th>Apply</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($courses) === 0): ?>
              <tr>
                <td colspan="5" style="text-align:center; padding:24px; color:#475569;">No courses are available yet.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($courses as $course): ?>
                <tr>
                  <td><?php echo htmlspecialchars($course['name']); ?></td>
                  <td><?php echo htmlspecialchars($course['code']); ?></td>
                  <td><?php echo htmlspecialchars($course['duration']); ?></td>
                  <td><?php echo htmlspecialchars($course['description']); ?></td>
                  <td>
                    <a class="btn table-apply-btn" href="application.php?course_id=<?php echo (int) $course['id']; ?>">Apply</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <footer>
    <p>© 2026 Dr TMA Pai Polytechnic Manipal. All rights reserved.</p>
  </footer>
</body>
</html>
