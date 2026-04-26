<?php
// backend/api/admin/enrollments.php
include_once '../../config/db.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $conn->query("
            SELECT e.enrollment_id, e.status, e.enrollment_date, 
                   s.name as student_name, s.email as student_email, 
                   c.course_name, c.fees
            FROM enrollments e
            JOIN students s ON e.student_id = s.student_id
            JOIN courses c ON e.course_id = c.course_id
            ORDER BY e.enrollment_date DESC
        ");
        echo json_encode($stmt->fetchAll());
    } elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->enrollment_id) && !empty($data->status)) {
            $stmt = $conn->prepare("UPDATE enrollments SET status = ? WHERE enrollment_id = ?");
            $stmt->execute([$data->status, $data->enrollment_id]);
            echo json_encode(["message" => "Enrollment status updated"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "ID and status required"]);
        }
    } else {
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
