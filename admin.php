<?php
require_once __DIR__ . '/db.php';

if (empty($_SESSION['admin'])) {
    redirect('login.html');
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    deleteStudent((int) $_GET['id']);
    redirect('admin.php');
}

$courses = getCourses();
$students = getStudents();
$applications = getAllApplications();
$studentApplicationOverview = getStudentApplicationOverview();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="logo">Admin Portal</div>
    <nav>
      <a href="index.html">Home</a>
      <a href="admin_registered.php">Registered Details</a>
      <a href="about.html">About</a>
      <a href="courses.php">Courses</a>
      <a href="contact.html">Contact</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main class="page-content">
    <section class="section">
      <h2>Course Catalog</h2>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Code</th>
              <th>Duration</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($courses as $course): ?>
              <tr>
                <td><?php echo htmlspecialchars($course['id']); ?></td>
                <td><?php echo htmlspecialchars($course['name']); ?></td>
                <td><?php echo htmlspecialchars($course['code']); ?></td>
                <td><?php echo htmlspecialchars($course['duration']); ?></td>
                <td><?php echo htmlspecialchars($course['description']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="section section-alt">
      <h2>Student Records</h2>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Course</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $student): ?>
              <tr>
                <td><?php echo htmlspecialchars($student['id']); ?></td>
                <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                <td><?php echo htmlspecialchars($student['email']); ?></td>
                <td><?php echo htmlspecialchars($student['mobile']); ?></td>
                <td><?php echo htmlspecialchars($student['course_name'] ?? 'Unassigned'); ?></td>
                <td>
                  <a href="admin_edit_student.php?id=<?php echo (int) $student['id']; ?>">Edit</a>
                  &nbsp;|&nbsp;
                  <a href="admin.php?action=delete&id=<?php echo (int) $student['id']; ?>" onclick="return confirm('Delete this student record?');">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="section">
      <h2>Applied Students (Separate)</h2>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Application ID</th>
              <th>Student Name</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Parent Name</th>
              <th>Course Applied</th>
              <th>Applied On</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($applications) === 0): ?>
              <tr>
                <td colspan="7" style="text-align:center; padding:24px; color:#475569;">No applications submitted yet.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($applications as $application): ?>
                <tr>
                  <td><?php echo htmlspecialchars($application['id']); ?></td>
                  <td><?php echo htmlspecialchars($application['student_name']); ?></td>
                  <td><?php echo htmlspecialchars($application['email']); ?></td>
                  <td><?php echo htmlspecialchars($application['mobile']); ?></td>
                  <td><?php echo htmlspecialchars($application['parent_name']); ?></td>
                  <td><?php echo htmlspecialchars($application['course_name'] . ' (' . $application['course_code'] . ')'); ?></td>
                  <td><?php echo htmlspecialchars($application['created_at']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="section section-alt">
      <h2>Students and Applications (Together)</h2>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Application ID</th>
              <th>Registered Student ID</th>
              <th>Student Name</th>
              <th>Email</th>
              <th>Registered Course</th>
              <th>Applied Course</th>
              <th>Applied On</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($studentApplicationOverview) === 0): ?>
              <tr>
                <td colspan="7" style="text-align:center; padding:24px; color:#475569;">No student-application data found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($studentApplicationOverview as $row): ?>
                <?php
                  $displayName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));
                  if ($displayName === '') {
                      $displayName = $row['application_name'];
                  }
                ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['application_id']); ?></td>
                  <td><?php echo htmlspecialchars($row['student_id'] ?? 'Not Registered'); ?></td>
                  <td><?php echo htmlspecialchars($displayName); ?></td>
                  <td><?php echo htmlspecialchars($row['registered_email'] ?? $row['application_email']); ?></td>
                  <td><?php echo htmlspecialchars($row['registered_course_name'] ?? 'Not Assigned'); ?></td>
                  <td><?php echo htmlspecialchars($row['applied_course_name'] . ' (' . $row['applied_course_code'] . ')'); ?></td>
                  <td><?php echo htmlspecialchars($row['applied_on']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <footer>
    <p>© 2026 Dr TMA Pai Polytechnic Manipal. Admin panel.</p>
  </footer>
</body>
</html>
