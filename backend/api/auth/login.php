<?php
// backend/api/auth/login.php
include_once '../../config/db.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    $email = $data->email;
    $password = $data->password;
    $role = isset($data->role) ? $data->role : 'student';

    $table = ($role === 'admin') ? 'admins' : 'students';
    
    $query = "SELECT * FROM " . $table . " WHERE email = :email LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        if(password_verify($password, $row['password'])) {
            // For simplicity, we just return user data and role. In prod, use JWT.
            unset($row['password']);
            http_response_code(200);
            echo json_encode(array("message" => "Login successful", "user" => $row, "role" => $role));
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Invalid password."));
        }
    } else {
        http_response_code(404);
        echo json_encode(array("message" => "User not found."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?>
