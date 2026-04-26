-- Database Configuration for Online Student Registration System
CREATE DATABASE IF NOT EXISTS student_registration_system;
USE student_registration_system;

-- 1. Admins Table
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- 2. Students Table
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15),
    dob DATE,
    gender VARCHAR(10),
    address VARCHAR(255),
    password VARCHAR(255) NOT NULL
);

-- 3. Courses Table
CREATE TABLE IF NOT EXISTS courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL,
    description TEXT,
    duration VARCHAR(50), -- e.g., '2 Years'
    fees DECIMAL(10, 2) NOT NULL
);

-- 4. Enrollments Table
CREATE TABLE IF NOT EXISTS enrollments (
    enrollment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    course_id INT,
    enrollment_date DATE DEFAULT CURRENT_DATE,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
);

-- 5. Attendance Table
CREATE TABLE IF NOT EXISTS attendance (
    attendance_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    date DATE NOT NULL,
    status ENUM('Present', 'Absent') NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

-- 6. Marks Table
CREATE TABLE IF NOT EXISTS marks (
    mark_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    subject VARCHAR(100) NOT NULL,
    marks DECIMAL(5, 2) NOT NULL,
    exam_type VARCHAR(50) NOT NULL,
    exam_date DATE,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

-- 7. Skills Table
CREATE TABLE IF NOT EXISTS skills (
    skill_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    skill_name VARCHAR(100) NOT NULL,
    level VARCHAR(50) NOT NULL, -- e.g., Beginner, Intermediate, Expert
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

-- 8. Physical Activities Table
CREATE TABLE IF NOT EXISTS physical_activities (
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    activity_name VARCHAR(100) NOT NULL,
    performance VARCHAR(100),
    date_recorded DATE NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

-- 9. Fee Payments Table
CREATE TABLE IF NOT EXISTS fee_payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATE DEFAULT CURRENT_DATE,
    payment_method VARCHAR(50), -- e.g., Online, Cash, Bank Transfer
    status ENUM('Completed', 'Pending', 'Failed') DEFAULT 'Completed',
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

-- 10. Notices Table
CREATE TABLE IF NOT EXISTS notices (
    notice_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    date_posted DATE DEFAULT CURRENT_DATE,
    author_admin_id INT,
    FOREIGN KEY (author_admin_id) REFERENCES admins(admin_id) ON DELETE SET NULL
);

-- 11. Library Books Table
CREATE TABLE IF NOT EXISTS library_books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    author VARCHAR(100),
    total_copies INT DEFAULT 1,
    available_copies INT DEFAULT 1
);

-- 12. Book Issues Table
CREATE TABLE IF NOT EXISTS book_issues (
    issue_id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT,
    student_id INT,
    issue_date DATE DEFAULT CURRENT_DATE,
    due_date DATE NOT NULL,
    return_date DATE NULL,
    FOREIGN KEY (book_id) REFERENCES library_books(book_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

-- -----------------------------------------------------------------------------
-- INSERT DUMMY DATA
-- -----------------------------------------------------------------------------

-- Admin password is 'admin123' (hashed via PHP password_hash)
INSERT INTO admins (name, email, password) VALUES 
('Super Admin', 'admin@mca.edu', '$2y$10$T1K.KzZpD/hS2rX2YgZ.w.P4M2qA/Fk7n1S/8g/5O.nQh.L3yZ6');

-- Student password is 'student123' (hashed via PHP password_hash)
INSERT INTO students (name, email, phone, dob, gender, address, password) VALUES 
('Harsh Shah', 'harsh@student.edu', '9876543210', '2000-01-01', 'Male', 'Ahmedabad, Gujarat', '$2y$10$O1M.KzZpD/hS2rX2YgZ.w.P4M2qA/Fk7n1S/8g/5O.nQh.L3yZ6');

INSERT INTO courses (course_name, description, duration, fees) VALUES 
('MCA', 'Master of Computer Applications', '2 Years', 50000.00),
('BCA', 'Bachelor of Computer Applications', '3 Years', 30000.00);

INSERT INTO enrollments (student_id, course_id, status) VALUES 
(1, 1, 'Approved');

INSERT INTO attendance (student_id, date, status) VALUES 
(1, '2026-04-25', 'Present'),
(1, '2026-04-26', 'Absent');

INSERT INTO marks (student_id, subject, marks, exam_type, exam_date) VALUES 
(1, 'Data Structures', 85.50, 'Mid-term', '2026-04-15'),
(1, 'Database Management', 90.00, 'Mid-term', '2026-04-16');

INSERT INTO skills (student_id, skill_name, level) VALUES 
(1, 'Python Programming', 'Expert'),
(1, 'Vue.js', 'Intermediate');

INSERT INTO physical_activities (student_id, activity_name, performance, date_recorded) VALUES 
(1, 'Cricket', 'Good', '2026-04-20');

INSERT INTO fee_payments (student_id, amount, payment_method, status) VALUES 
(1, 25000.00, 'Online', 'Completed');

INSERT INTO notices (title, content, author_admin_id) VALUES 
('Welcome to MCA', 'Welcome to the new academic year. Please check your schedule.', 1),
('Holiday Announcement', 'The college will remain closed on public holidays.', 1);

INSERT INTO library_books (title, author, total_copies, available_copies) VALUES 
('Software Engineering', 'Roger S. Pressman', 5, 4),
('Database System Concepts', 'Abraham Silberschatz', 3, 3);

INSERT INTO book_issues (book_id, student_id, issue_date, due_date) VALUES 
(1, 1, '2026-04-20', '2026-05-04');
