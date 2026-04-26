<?php
// backend/api/student/academic.php
include_once '../../config/db.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    $data = [];

    try {
        $stmt = $conn->prepare("SELECT * FROM marks WHERE student_id = ? ORDER BY exam_date DESC");
        $stmt->execute([$student_id]);
        $data['marks'] = $stmt->fetchAll();

        $stmt = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? ORDER BY date DESC");
        $stmt->execute([$student_id]);
        $data['attendance'] = $stmt->fetchAll();

        $stmt = $conn->prepare("SELECT * FROM skills WHERE student_id = ?");
        $stmt->execute([$student_id]);
        $data['skills'] = $stmt->fetchAll();

        $stmt = $conn->prepare("SELECT * FROM physical_activities WHERE student_id = ? ORDER BY date_recorded DESC");
        $stmt->execute([$student_id]);
        $data['physical_activities'] = $stmt->fetchAll();

        echo json_encode($data);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    try {
        if ($data->type === 'skill') {
            $stmt = $conn->prepare("INSERT INTO skills (student_id, skill_name, level) VALUES (?, ?, ?)");
            $stmt->execute([$data->student_id, $data->skill_name, $data->level]);
        } elseif ($data->type === 'activity') {
            $stmt = $conn->prepare("INSERT INTO physical_activities (student_id, activity_name, performance, date_recorded) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data->student_id, $data->activity_name, $data->performance, $data->date_recorded]);
        }
        echo json_encode(["message" => "Record added successfully"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"));
    try {
        if ($data->type === 'skill') {
            $stmt = $conn->prepare("UPDATE skills SET skill_name = ?, level = ? WHERE skill_id = ? AND student_id = ?");
            $stmt->execute([$data->skill_name, $data->level, $data->skill_id, $data->student_id]);
        } elseif ($data->type === 'activity') {
            $stmt = $conn->prepare("UPDATE physical_activities SET activity_name = ?, performance = ?, date_recorded = ? WHERE activity_id = ? AND student_id = ?");
            $stmt->execute([$data->activity_name, $data->performance, $data->date_recorded, $data->activity_id, $data->student_id]);
        }
        echo json_encode(["message" => "Record updated successfully"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"));
    try {
        if ($data->type === 'skill') {
            $stmt = $conn->prepare("DELETE FROM skills WHERE skill_id = ? AND student_id = ?");
            $stmt->execute([$data->skill_id, $data->student_id]);
        } elseif ($data->type === 'activity') {
            $stmt = $conn->prepare("DELETE FROM physical_activities WHERE activity_id = ? AND student_id = ?");
            $stmt->execute([$data->activity_id, $data->student_id]);
        }
        echo json_encode(["message" => "Record deleted successfully"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid request"]);
}
?>
