<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$DB_HOST = '127.0.0.1';
$DB_NAME = 'college_portal';
$DB_USER = 'root';
$DB_PASS = '';

$pdo = null;

try {
    $pdo = connectDatabase($DB_HOST, $DB_NAME, $DB_USER, $DB_PASS);
    ensureSchema($pdo, $DB_NAME);
} catch (PDOException $exception) {
    http_response_code(500);
    echo '<h1>Database connection failed</h1>';
    echo '<p>' . htmlspecialchars($exception->getMessage()) . '</p>';
    exit;
}

function connectDatabase(string $host, string $name, string $user, string $pass): PDO
{
    $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $exception) {
        if (strpos($exception->getMessage(), 'Unknown database') !== false || ($exception->errorInfo[1] ?? null) === 1049) {
            $pdo = new PDO("mysql:host={$host};charset=utf8mb4", $user, $pass, $options);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$name}`");
            return $pdo;
        }
        throw $exception;
    }
}

function ensureSchema(PDO $pdo, string $dbName): void
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS courses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        code VARCHAR(20) NOT NULL UNIQUE,
        duration VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(80) NOT NULL,
        last_name VARCHAR(80) NOT NULL,
        email VARCHAR(200) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        mobile VARCHAR(20) NOT NULL,
        course_id INT DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $pdo->exec('CREATE TABLE IF NOT EXISTS applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT NULL,
        course_id INT NOT NULL,
        student_name VARCHAR(160) NOT NULL,
        parent_name VARCHAR(160) NOT NULL,
        dob DATE NOT NULL,
        gender VARCHAR(20) NOT NULL,
        email VARCHAR(200) NOT NULL,
        mobile VARCHAR(20) NOT NULL,
        guardian_mobile VARCHAR(20) NOT NULL,
        qualification VARCHAR(120) NOT NULL,
        marks VARCHAR(40) NOT NULL,
        address TEXT NOT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL ON UPDATE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE RESTRICT ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

    $stmt = $pdo->prepare(
        'INSERT INTO courses (name, code, duration, description)
         SELECT ?, ?, ?, ?
         WHERE NOT EXISTS (SELECT 1 FROM courses WHERE code = ?)'
    );
    $courses = [
        ['Computer Science Engineering', 'CSE', '3 years', 'Software development, networking, and AI foundations.'],
        ['Mechanical Engineering', 'ME', '3 years', 'Thermodynamics, design, and manufacturing systems.'],
        ['Civil Engineering', 'CE', '3 years', 'Structural design, infrastructure, and construction management.'],
        ['Electrical Engineering', 'EE', '3 years', 'Power systems, electronics, and automation.'],
        ['Electronics & Communication', 'ECE', '3 years', 'Signal processing, communication networks, and semiconductor technology.'],
        ['Automobile Engineering', 'AE', '3 years', 'Vehicle systems, automotive design, and workshop practice.'],
        ['Mechatronics', 'MCT', '3 years', 'Integrated mechanical, electronics, and control systems for smart automation.'],
        ['Printing', 'PRN', '3 years', 'Printing technology, packaging processes, and production workflows.'],
        ['Artificial Intelligence', 'AI', '3 years', 'Machine learning, data intelligence, and AI application development.'],
    ];
    foreach ($courses as $course) {
        $stmt->execute([$course[0], $course[1], $course[2], $course[3], $course[1]]);
    }
}

function db(): PDO
{
    global $pdo;
    return $pdo;
}

function getCourses(): array
{
    return db()->query('SELECT id, name, code, duration, description FROM courses ORDER BY name')->fetchAll();
}

function getCourse(int $id): array
{
    $stmt = db()->prepare('SELECT id, name, code, duration, description FROM courses WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: [];
}

function getStudentByEmail(string $email): array
{
    $stmt = db()->prepare('SELECT * FROM students WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    return $stmt->fetch() ?: [];
}

function getStudentById(int $id): array
{
    $stmt = db()->prepare('SELECT students.*, courses.name AS course_name, courses.code AS course_code FROM students LEFT JOIN courses ON students.course_id = courses.id WHERE students.id = ? LIMIT 1');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: [];
}

function getStudents(): array
{
    $stmt = db()->query('SELECT students.id, students.first_name, students.last_name, students.email, students.mobile, students.course_id, students.created_at, courses.name AS course_name FROM students LEFT JOIN courses ON students.course_id = courses.id ORDER BY students.last_name, students.first_name');
    return $stmt->fetchAll();
}

function updateStudent(int $id, string $firstName, string $lastName, string $email, string $mobile, ?int $courseId, ?string $passwordHash = null): bool
{
    if ($passwordHash) {
        $stmt = db()->prepare('UPDATE students SET first_name = ?, last_name = ?, email = ?, mobile = ?, course_id = ?, password_hash = ? WHERE id = ?');
        return $stmt->execute([$firstName, $lastName, $email, $mobile, $courseId, $passwordHash, $id]);
    }

    $stmt = db()->prepare('UPDATE students SET first_name = ?, last_name = ?, email = ?, mobile = ?, course_id = ? WHERE id = ?');
    return $stmt->execute([$firstName, $lastName, $email, $mobile, $courseId, $id]);
}

function deleteStudent(int $id): bool
{
    $stmt = db()->prepare('DELETE FROM students WHERE id = ?');
    return $stmt->execute([$id]);
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function createApplication(array $data): bool
{
    $stmt = db()->prepare(
        'INSERT INTO applications (student_id, course_id, student_name, parent_name, dob, gender, email, mobile, guardian_mobile, qualification, marks, address, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
    );

    return $stmt->execute([
        $data['student_id'] ?? null,
        $data['course_id'],
        $data['student_name'],
        $data['parent_name'],
        $data['dob'],
        $data['gender'],
        $data['email'],
        $data['mobile'],
        $data['guardian_mobile'],
        $data['qualification'],
        $data['marks'],
        $data['address'],
    ]);
}

function getApplicationsByStudent(int $studentId, string $email): array
{
    $stmt = db()->prepare(
        'SELECT applications.id, applications.student_name, applications.parent_name, applications.dob, applications.gender, applications.email, applications.mobile,
                applications.guardian_mobile, applications.qualification, applications.marks, applications.address, applications.created_at,
                courses.name AS course_name, courses.code AS course_code, courses.duration AS course_duration
         FROM applications
         INNER JOIN courses ON applications.course_id = courses.id
         WHERE applications.student_id = ? OR (applications.student_id IS NULL AND applications.email = ?)
         ORDER BY applications.created_at DESC'
    );
    $stmt->execute([$studentId, $email]);
    return $stmt->fetchAll();
}

function getAllApplications(): array
{
    $stmt = db()->query(
        'SELECT applications.id, applications.student_id, applications.student_name, applications.parent_name, applications.dob, applications.gender,
                applications.email, applications.mobile, applications.guardian_mobile, applications.qualification, applications.marks,
                applications.address, applications.created_at, courses.name AS course_name, courses.code AS course_code, courses.duration AS course_duration
         FROM applications
         INNER JOIN courses ON applications.course_id = courses.id
         ORDER BY applications.created_at DESC'
    );
    return $stmt->fetchAll();
}

function getStudentApplicationOverview(): array
{
    $stmt = db()->query(
        'SELECT applications.id AS application_id, applications.created_at AS applied_on, applications.student_name AS application_name,
                applications.email AS application_email, applications.mobile AS application_mobile,
                courses.name AS applied_course_name, courses.code AS applied_course_code,
                students.id AS student_id, students.first_name, students.last_name, students.email AS registered_email,
                students.mobile AS registered_mobile, course_reg.name AS registered_course_name
         FROM applications
         INNER JOIN courses ON applications.course_id = courses.id
         LEFT JOIN students ON students.id = applications.student_id OR students.email = applications.email
         LEFT JOIN courses AS course_reg ON students.course_id = course_reg.id
         ORDER BY applications.created_at DESC'
    );
    return $stmt->fetchAll();
}
