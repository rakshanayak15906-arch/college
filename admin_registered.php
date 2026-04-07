<?php
require_once __DIR__ . '/db.php';

if (empty($_SESSION['admin'])) {
    redirect('admin_login.php');
}

if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])) {
    deleteStudent((int) $_GET['id']);
    redirect('admin_registered.php');
}

$students = getStudents();
$applications = function_exists('getAllApplications') ? getAllApplications() : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registered Student Details</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="logo">Admin Portal</div>
    <nav>
      <a href="index.html">Home</a>
      <a href="admin_registered.php">Registered Details</a>
      <a href="admin.php">Full Admin Page</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main class="page-content">
    <section class="section">
      <h2>Registered Student Details</h2>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Mobile</th>
              <th>Registered Course</th>
              <th>Registered On</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($students) === 0): ?>
              <tr>
                <td colspan="7" style="text-align:center; padding:24px; color:#475569;">No registered students found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($students as $student): ?>
                <tr>
                  <td><?php echo htmlspecialchars($student['id']); ?></td>
                  <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                  <td><?php echo htmlspecialchars($student['email']); ?></td>
                  <td><?php echo htmlspecialchars($student['mobile']); ?></td>
                  <td><?php echo htmlspecialchars($student['course_name'] ?? 'Unassigned'); ?></td>
                  <td><?php echo htmlspecialchars($student['created_at'] ?? '-'); ?></td>
                  <td>
                    <a href="admin_edit_student.php?id=<?php echo (int) $student['id']; ?>">Edit</a>
                    &nbsp;|&nbsp;
                    <a href="admin_registered.php?action=delete&id=<?php echo (int) $student['id']; ?>" onclick="return confirm('Delete this student record?');">Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section class="section section-alt">
      <h2>Applied Course Details</h2>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Application ID</th>
              <th>Student Name</th>
              <th>Email</th>
              <th>Applied Course</th>
              <th>Parent Name</th>
              <th>Applied On</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($applications) === 0): ?>
              <tr>
                <td colspan="6" style="text-align:center; padding:24px; color:#475569;">No applications found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($applications as $application): ?>
                <tr>
                  <td><?php echo htmlspecialchars($application['id']); ?></td>
                  <td><?php echo htmlspecialchars($application['student_name']); ?></td>
                  <td><?php echo htmlspecialchars($application['email']); ?></td>
                  <td><?php echo htmlspecialchars($application['course_name'] . ' (' . $application['course_code'] . ')'); ?></td>
                  <td><?php echo htmlspecialchars($application['parent_name']); ?></td>
                  <td><?php echo htmlspecialchars($application['created_at']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; 2026 Dr TMA Pai Polytechnic Manipal. Admin panel.</p>
  </footer>
</body>
</html>
