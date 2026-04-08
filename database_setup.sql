-- Create the portal database and required tables
CREATE DATABASE IF NOT EXISTS college_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE college_portal;

-- Courses table stores available diploma programs
CREATE TABLE IF NOT EXISTS courses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  code VARCHAR(20) NOT NULL UNIQUE,
  duration VARCHAR(50) NOT NULL,
  description TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Students table stores registration and login details for each student
CREATE TABLE IF NOT EXISTS students (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(80) NOT NULL,
  last_name VARCHAR(80) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  mobile VARCHAR(20) NOT NULL,
  course_id INT DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Course applications table
CREATE TABLE IF NOT EXISTS applications (
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
) ENGINE=InnoDB;

-- Sample course records
INSERT INTO courses (name, code, duration, description)
SELECT * FROM (
  SELECT 'Computer Science Engineering' AS name, 'CSE' AS code, '3 years' AS duration, 'Software development, networking, and AI foundations.' AS description
  UNION ALL SELECT 'Mechanical Engineering', 'ME', '3 years', 'Thermodynamics, design, and manufacturing systems.'
  UNION ALL SELECT 'Civil Engineering', 'CE', '3 years', 'Structural design, infrastructure, and construction practice.'
  UNION ALL SELECT 'Electrical Engineering', 'EE', '3 years', 'Power systems, circuits, and automation.'
  UNION ALL SELECT 'Electronics & Communication', 'ECE', '3 years', 'Signal processing, communication systems, and embedded electronics.'
  UNION ALL SELECT 'Automobile Engineering', 'AE', '3 years', 'Vehicle systems, automotive mechanics, and workshop training.'
  UNION ALL SELECT 'Mechatronics', 'MCT', '3 years', 'Integrated mechanical, electronics, and control systems for smart automation.'
  UNION ALL SELECT 'Printing', 'PRN', '3 years', 'Printing technology, packaging processes, and production workflows.'
  UNION ALL SELECT 'Artificial Intelligence', 'AI', '3 years', 'Machine learning, data intelligence, and AI application development.'
) AS seed
WHERE NOT EXISTS (SELECT 1 FROM courses c WHERE c.code = seed.code);
