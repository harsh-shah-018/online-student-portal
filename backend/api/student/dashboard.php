<?php
// backend/api/student/dashboard.php
include_once '../../config/db.php';

header('Content-Type: application/json');

if (!isset($_GET['student_id'])) {
    http_response_code(400);
    echo json_encode(array("message" => "student_id is required."));
    exit();
}

$student_id = $_GET['student_id'];

try {
    $stats = array();

    // Student Profile
    $stmt = $conn->prepare("SELECT name, email, phone, address FROM students WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $stats['profile'] = $stmt->fetch();

    // Enrolled Courses
    $stmt = $conn->prepare("SELECT c.course_name, e.status FROM enrollments e JOIN courses c ON e.course_id = c.course_id WHERE e.student_id = ?");
    $stmt->execute([$student_id]);
    $stats['enrollments'] = $stmt->fetchAll();

    // Total Fees Paid
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM fee_payments WHERE student_id = ? AND status = 'Completed'");
    $stmt->execute([$student_id]);
    $stats['fees_paid'] = $stmt->fetch()['total'] ?? 0;

    // Recent Notices
    $stmt = $conn->query("SELECT title, content, date_posted FROM notices ORDER BY date_posted DESC LIMIT 5");
    $stats['notices'] = $stmt->fetchAll();

    http_response_code(200);
    echo json_encode($stats);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(array("error" => $e->getMessage()));
}
?>
