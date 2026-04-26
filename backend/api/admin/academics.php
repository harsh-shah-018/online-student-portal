<?php
// backend/api/admin/academics.php
include_once '../../config/db.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['student_id'])) {
        $student_id = $_GET['student_id'];
        $data = [];
        try {
            $stmt = $conn->prepare("SELECT * FROM marks WHERE student_id = ? ORDER BY exam_date DESC");
            $stmt->execute([$student_id]);
            $data['marks'] = $stmt->fetchAll();

            $stmt = $conn->prepare("SELECT * FROM attendance WHERE student_id = ? ORDER BY date DESC");
            $stmt->execute([$student_id]);
            $data['attendance'] = $stmt->fetchAll();

            echo json_encode($data);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    } else {
        // Fetch all students for the dropdown
        try {
            $stmt = $conn->query("SELECT student_id, name FROM students");
            echo json_encode($stmt->fetchAll());
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    try {
        if ($data->type === 'mark') {
            $stmt = $conn->prepare("INSERT INTO marks (student_id, subject, exam_type, marks, exam_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data->student_id, $data->subject, $data->exam_type, $data->marks, $data->exam_date]);
        } elseif ($data->type === 'attendance') {
            $stmt = $conn->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?, ?, ?)");
            $stmt->execute([$data->student_id, $data->date, $data->status]);
        }
        echo json_encode(["message" => "Record added successfully"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"));
    try {
        if ($data->type === 'mark') {
            $stmt = $conn->prepare("UPDATE marks SET subject = ?, exam_type = ?, marks = ?, exam_date = ? WHERE mark_id = ?");
            $stmt->execute([$data->subject, $data->exam_type, $data->marks, $data->exam_date, $data->mark_id]);
        } elseif ($data->type === 'attendance') {
            $stmt = $conn->prepare("UPDATE attendance SET date = ?, status = ? WHERE attendance_id = ?");
            $stmt->execute([$data->date, $data->status, $data->attendance_id]);
        }
        echo json_encode(["message" => "Record updated successfully"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"));
    try {
        if ($data->type === 'mark') {
            $stmt = $conn->prepare("DELETE FROM marks WHERE mark_id = ?");
            $stmt->execute([$data->mark_id]);
        } elseif ($data->type === 'attendance') {
            $stmt = $conn->prepare("DELETE FROM attendance WHERE attendance_id = ?");
            $stmt->execute([$data->attendance_id]);
        }
        echo json_encode(["message" => "Record deleted successfully"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}
?>
