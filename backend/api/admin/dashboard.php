<?php
// backend/api/admin/dashboard.php
include_once '../../config/db.php';

header('Content-Type: application/json');

try {
    $stats = array();

    // Total Students
    $stmt = $conn->query("SELECT COUNT(*) as count FROM students");
    $stats['total_students'] = $stmt->fetch()['count'];

    // Total Courses
    $stmt = $conn->query("SELECT COUNT(*) as count FROM courses");
    $stats['total_courses'] = $stmt->fetch()['count'];

    // Pending Enrollments
    $stmt = $conn->query("SELECT COUNT(*) as count FROM enrollments WHERE status = 'Pending'");
    $stats['pending_enrollments'] = $stmt->fetch()['count'];

    // Total Revenue (Completed Fees)
    $stmt = $conn->query("SELECT SUM(amount) as total FROM fee_payments WHERE status = 'Completed'");
    $stats['total_revenue'] = $stmt->fetch()['total'] ?? 0;

    // Recent Notices
    $stmt = $conn->query("SELECT title, date_posted FROM notices ORDER BY date_posted DESC LIMIT 5");
    $stats['recent_notices'] = $stmt->fetchAll();

    http_response_code(200);
    echo json_encode($stats);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(array("error" => $e->getMessage()));
}
?>
