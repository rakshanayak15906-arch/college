<?php
require_once __DIR__ . '/db.php';

$courses = getCourses();
$courseOptions = [];
foreach ($courses as $course) {
    $courseOptions[(int) $course['id']] = $course;
}

$requestedCourseId = (int) ($_GET['course_id'] ?? $_POST['course_id'] ?? 0);
$selectedCourseId = array_key_exists($requestedCourseId, $courseOptions) ? $requestedCourseId : 0;
$maxDob = '2007-12-31';

$isSubmitted = $_SERVER['REQUEST_METHOD'] === 'POST';
$errors = [];
$successMessage = '';
$currentStudent = [];

if (!empty($_SESSION['student_id'])) {
    $currentStudent = getStudentById((int) $_SESSION['student_id']);
}

$form = [
    'student_name' => trim($_POST['student_name'] ?? (($currentStudent['first_name'] ?? '') . ' ' . ($currentStudent['last_name'] ?? ''))),
    'parent_name' => trim($_POST['parent_name'] ?? ''),
    'dob' => trim($_POST['dob'] ?? ''),
    'gender' => trim($_POST['gender'] ?? ''),
    'email' => trim($_POST['email'] ?? ($currentStudent['email'] ?? '')),
    'mobile' => trim($_POST['mobile'] ?? ($currentStudent['mobile'] ?? '')),
    'address' => trim($_POST['address'] ?? ''),
    'course_id' => $selectedCourseId,
    'qualification' => trim($_POST['qualification'] ?? ''),
    'marks' => trim($_POST['marks'] ?? ''),
    'guardian_mobile' => trim($_POST['guardian_mobile'] ?? ''),
];

if ($isSubmitted) {
    if ($form['student_name'] === '') {
        $errors[] = 'Student name is required.';
    }
    if ($form['parent_name'] === '') {
        $errors[] = 'Parent name is required.';
    }
    if ($form['dob'] === '') {
        $errors[] = 'Date of birth is required.';
    } elseif ($form['dob'] > $maxDob) {
        $errors[] = 'Date of birth must be before 2008.';
    }
    if ($form['gender'] === '') {
        $errors[] = 'Gender is required.';
    }
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', strtolower($form['email']))) {
        $errors[] = 'Please enter a valid Gmail address ending with @gmail.com.';
    }
    if (!preg_match('/^[0-9]{10}$/', $form['mobile'])) {
        $errors[] = 'Student mobile number must be 10 digits.';
    }
    if (!preg_match('/^[0-9]{10}$/', $form['guardian_mobile'])) {
        $errors[] = 'Parent/guardian mobile number must be 10 digits.';
    }
    if ($form['address'] === '') {
        $errors[] = 'Address is required.';
    }
    if (!array_key_exists($form['course_id'], $courseOptions)) {
        $errors[] = 'Please select a valid course.';
    }
    $allowedQualifications = ['SSLC', 'PU', 'ITA'];
    if (!in_array($form['qualification'], $allowedQualifications, true)) {
        $errors[] = 'Please select a valid qualification.';
    }
    if ($form['marks'] === '') {
        $errors[] = 'Percentage/CGPA is required.';
    }

    if (count($errors) === 0) {
        createApplication([
            'student_id' => $currentStudent ? (int) $currentStudent['id'] : null,
            'course_id' => (int) $form['course_id'],
            'student_name' => $form['student_name'],
            'parent_name' => $form['parent_name'],
            'dob' => $form['dob'],
            'gender' => $form['gender'],
            'email' => strtolower($form['email']),
            'mobile' => $form['mobile'],
            'guardian_mobile' => $form['guardian_mobile'],
            'qualification' => $form['qualification'],
            'marks' => $form['marks'],
            'address' => $form['address'],
        ]);

        $courseName = $courseOptions[$form['course_id']]['name'];
        $successMessage = 'Application submitted successfully for ' . $courseName . '. Our admissions team will contact you soon.';

        $form = [
            'student_name' => '',
            'parent_name' => '',
            'dob' => '',
            'gender' => '',
            'email' => '',
            'mobile' => '',
            'address' => '',
            'course_id' => $selectedCourseId,
            'qualification' => '',
            'marks' => '',
            'guardian_mobile' => '',
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Application</title>
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
    <section class="section application-section">
      <h2>Course Application Form</h2>
      <p>Please complete this form carefully. Admission is subject to document verification and eligibility criteria.</p>

      <?php if ($successMessage !== ''): ?>
        <div class="alert success" style="display:block;"><?php echo htmlspecialchars($successMessage); ?></div>
      <?php endif; ?>

      <?php if (count($errors) > 0): ?>
        <div class="alert error" style="display:block;">
          <?php foreach ($errors as $error): ?>
            <div><?php echo htmlspecialchars($error); ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="application.php" class="application-form">
        <div class="form-grid">
          <div>
            <label for="student_name">Student Name</label>
            <input id="student_name" type="text" name="student_name" value="<?php echo htmlspecialchars($form['student_name']); ?>" required>
          </div>
          <div>
            <label for="parent_name">Parent Name</label>
            <input id="parent_name" type="text" name="parent_name" value="<?php echo htmlspecialchars($form['parent_name']); ?>" required>
          </div>
          <div>
            <label for="dob">Date of Birth</label>
            <input id="dob" type="date" name="dob" max="<?php echo $maxDob; ?>" value="<?php echo htmlspecialchars($form['dob']); ?>" required>
          </div>
          <div>
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
              <option value="">Select gender</option>
              <option value="Male" <?php echo $form['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
              <option value="Female" <?php echo $form['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
              <option value="Other" <?php echo $form['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
          </div>
          <div>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" title="Please enter a valid Gmail address ending with @gmail.com" value="<?php echo htmlspecialchars($form['email']); ?>" required>
          </div>
          <div>
            <label for="mobile">Student Mobile</label>
            <input id="mobile" type="tel" name="mobile" minlength="10" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" value="<?php echo htmlspecialchars($form['mobile']); ?>" required>
          </div>
          <div>
            <label for="guardian_mobile">Parent/Guardian Mobile</label>
            <input id="guardian_mobile" type="tel" name="guardian_mobile" minlength="10" maxlength="10" pattern="[0-9]{10}" inputmode="numeric" value="<?php echo htmlspecialchars($form['guardian_mobile']); ?>" required>
          </div>
          <div>
            <label for="course_select">Course Selection</label>
            <select id="course_select" name="course_id" required>
              <option value="">Choose a course</option>
              <?php foreach ($courses as $course): ?>
                <?php $id = (int) $course['id']; ?>
                <option value="<?php echo $id; ?>" <?php echo $form['course_id'] === $id ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($course['name'] . ' (' . $course['code'] . ')'); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="qualification">Last Qualification</label>
            <select id="qualification" name="qualification" required>
              <option value="">Select qualification</option>
              <option value="SSLC" <?php echo $form['qualification'] === 'SSLC' ? 'selected' : ''; ?>>SSLC</option>
              <option value="PU" <?php echo $form['qualification'] === 'PU' ? 'selected' : ''; ?>>PU</option>
              <option value="ITA" <?php echo $form['qualification'] === 'ITA' ? 'selected' : ''; ?>>ITA</option>
            </select>
          </div>
          <div>
            <label for="marks">Percentage / CGPA</label>
            <input id="marks" type="text" name="marks" value="<?php echo htmlspecialchars($form['marks']); ?>" placeholder="e.g. 84%" required>
          </div>
        </div>

        <label for="address">Address</label>
        <textarea id="address" name="address" required><?php echo htmlspecialchars($form['address']); ?></textarea>

        <button type="submit" class="form-button">Submit Application</button>
      </form>
    </section>
  </main>

  <footer>
    <p>&copy; 2026 Dr TMA Pai Polytechnic Manipal. All rights reserved.</p>
  </footer>
</body>
</html>
