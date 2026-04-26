<?php
// backend/api/admin/courses.php
include_once '../../config/db.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $conn->query("SELECT * FROM courses ORDER BY course_id DESC");
        echo json_encode($stmt->fetchAll());
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->course_name) && !empty($data->fees)) {
            $stmt = $conn->prepare("INSERT INTO courses (course_name, description, duration, fees) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data->course_name, $data->description ?? '', $data->duration ?? '', $data->fees]);
            echo json_encode(["message" => "Course created successfully"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Course name and fees are required"]);
        }
    } elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->course_id) && !empty($data->course_name) && !empty($data->fees)) {
            $stmt = $conn->prepare("UPDATE courses SET course_name = ?, description = ?, duration = ?, fees = ? WHERE course_id = ?");
            $stmt->execute([$data->course_name, $data->description ?? '', $data->duration ?? '', $data->fees, $data->course_id]);
            echo json_encode(["message" => "Course updated successfully"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Course details required"]);
        }
    } elseif ($method === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->course_id)) {
            $stmt = $conn->prepare("DELETE FROM courses WHERE course_id = ?");
            $stmt->execute([$data->course_id]);
            echo json_encode(["message" => "Course deleted successfully"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Course ID required"]);
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
