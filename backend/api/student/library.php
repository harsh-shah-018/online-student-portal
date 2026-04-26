<?php
// backend/api/student/library.php
include_once '../../config/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];
    $data = [];

    try {
        $stmt = $conn->query("SELECT * FROM library_books");
        $data['books'] = $stmt->fetchAll();

        $stmt = $conn->prepare("
            SELECT bi.*, lb.title, lb.author 
            FROM book_issues bi 
            JOIN library_books lb ON bi.book_id = lb.book_id 
            WHERE bi.student_id = ?
        ");
        $stmt->execute([$student_id]);
        $data['my_issues'] = $stmt->fetchAll();

        echo json_encode($data);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid request"]);
}
?>
