<?php
// backend/api/student/enrollments.php
include_once '../../config/db.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Get all courses, and check if the student is already enrolled
        if (!isset($_GET['student_id'])) {
            http_response_code(400);
            echo json_encode(["error" => "student_id is required"]);
            exit();
        }
        $student_id = $_GET['student_id'];
        
        $stmt = $conn->prepare("
            SELECT c.*, e.status as enrollment_status 
            FROM courses c 
            LEFT JOIN enrollments e ON c.course_id = e.course_id AND e.student_id = ?
        ");
        $stmt->execute([$student_id]);
        echo json_encode($stmt->fetchAll());
        
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->student_id) && !empty($data->course_id)) {
            // Check if already enrolled
            $check = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
            $check->execute([$data->student_id, $data->course_id]);
            if ($check->rowCount() > 0) {
                http_response_code(400);
                echo json_encode(["error" => "Already enrolled or pending"]);
                exit();
            }
            
            $stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id, status) VALUES (?, ?, 'Pending')");
            $stmt->execute([$data->student_id, $data->course_id]);
            echo json_encode(["message" => "Enrollment request submitted successfully"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Student ID and Course ID are required"]);
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
