<?php
// backend/api/admin/students.php
include_once '../../config/db.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $stmt = $conn->query("SELECT student_id, name, email, phone, dob, gender, address FROM students");
            echo json_encode($stmt->fetchAll());
            break;
        case 'POST':
            $data = json_decode(file_get_contents("php://input"));
            $hash = password_hash($data->password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO students (name, email, phone, dob, gender, address, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$data->name, $data->email, $data->phone, $data->dob, $data->gender, $data->address, $hash]);
            echo json_encode(["message" => "Student created successfully"]);
            break;
        case 'DELETE':
            $data = json_decode(file_get_contents("php://input"));
            $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
            $stmt->execute([$data->student_id]);
            echo json_encode(["message" => "Student deleted successfully"]);
            break;
        case 'PUT':
            $data = json_decode(file_get_contents("php://input"));
            $stmt = $conn->prepare("UPDATE students SET name=?, email=?, phone=?, dob=?, gender=?, address=? WHERE student_id=?");
            $stmt->execute([$data->name, $data->email, $data->phone, $data->dob, $data->gender, $data->address, $data->student_id]);
            echo json_encode(["message" => "Student updated successfully"]);
            break;
        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
            break;
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
