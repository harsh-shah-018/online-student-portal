<?php
// backend/api/admin/library.php
include_once '../../config/db.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $data = [];
        $stmt = $conn->query("SELECT * FROM library_books ORDER BY book_id DESC");
        $data['books'] = $stmt->fetchAll();

        $stmt = $conn->query("
            SELECT bi.*, lb.title, s.name as student_name 
            FROM book_issues bi 
            JOIN library_books lb ON bi.book_id = lb.book_id 
            JOIN students s ON bi.student_id = s.student_id
            ORDER BY bi.issue_date DESC
        ");
        $data['issues'] = $stmt->fetchAll();
        
        echo json_encode($data);
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents("php://input"));
        $stmt = $conn->prepare("INSERT INTO library_books (title, author, total_copies, available_copies) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data->title, $data->author, $data->total_copies, $data->total_copies]);
        echo json_encode(["message" => "Book added successfully"]);
    } elseif ($method === 'PUT') {
        $data = json_decode(file_get_contents("php://input"));
        $stmt = $conn->prepare("UPDATE library_books SET title=?, author=?, total_copies=?, available_copies=? WHERE book_id=?");
        $stmt->execute([$data->title, $data->author, $data->total_copies, $data->available_copies, $data->book_id]);
        echo json_encode(["message" => "Book updated successfully"]);
    } elseif ($method === 'DELETE') {
        $data = json_decode(file_get_contents("php://input"));
        $stmt = $conn->prepare("DELETE FROM library_books WHERE book_id=?");
        $stmt->execute([$data->book_id]);
        echo json_encode(["message" => "Book deleted successfully"]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
