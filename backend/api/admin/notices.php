<?php
// backend/api/admin/notices.php
include_once '../../config/db.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $stmt = $conn->query("SELECT notices.*, admins.name as author_name FROM notices LEFT JOIN admins ON notices.author_admin_id = admins.admin_id ORDER BY date_posted DESC");
        echo json_encode($stmt->fetchAll());
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->title) && !empty($data->content) && !empty($data->author_admin_id)) {
            $stmt = $conn->prepare("INSERT INTO notices (title, content, author_admin_id, date_posted) VALUES (?, ?, ?, CURRENT_DATE)");
            $stmt->execute([$data->title, $data->content, $data->author_admin_id]);
            echo json_encode(["message" => "Notice created successfully"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Title, content, and author are required"]);
        }
    } elseif ($method === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->notice_id)) {
            $stmt = $conn->prepare("DELETE FROM notices WHERE notice_id = ?");
            $stmt->execute([$data->notice_id]);
            echo json_encode(["message" => "Notice deleted successfully"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Notice ID required"]);
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
