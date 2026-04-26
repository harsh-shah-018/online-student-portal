<?php
// backend/api/student/fees.php
include_once '../../config/db.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        if (!isset($_GET['student_id'])) {
            http_response_code(400);
            echo json_encode(["error" => "student_id is required"]);
            exit();
        }
        $student_id = $_GET['student_id'];
        
        $stmt = $conn->prepare("SELECT * FROM fee_payments WHERE student_id = ? ORDER BY payment_date DESC");
        $stmt->execute([$student_id]);
        echo json_encode($stmt->fetchAll());
        
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->student_id) && !empty($data->amount) && !empty($data->payment_method)) {
            $stmt = $conn->prepare("INSERT INTO fee_payments (student_id, amount, payment_method, status) VALUES (?, ?, ?, 'Completed')");
            $stmt->execute([$data->student_id, $data->amount, $data->payment_method]);
            echo json_encode(["message" => "Fee payment successful"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Incomplete payment data"]);
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
