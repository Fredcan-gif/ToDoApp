<?php
include 'db.php';
session_start();
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? null;

if (!$action) {
    echo json_encode(["success" => false, "message" => "No action specified."]);
    exit;
}

if ($action === "signup") {
    // Signup process
    $username = trim($data['username'] ?? '');
    $password = trim($data['password'] ?? '');

    if (empty($username) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Username and password required."]);
        exit;
    }

    // Check if the username already exists
    $checkSql = "SELECT * FROM users WHERE username = :username";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bindParam(':username', $username);
    $checkStmt->execute();

    if ($checkStmt->fetch()) {
        echo json_encode(["success" => false, "message" => "Username already taken."]);
        exit;
    }

    // Hash password and insert new user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $insertSql = "INSERT INTO users (username, password) VALUES (:username, :password)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bindParam(':username', $username);
    $insertStmt->bindParam(':password', $hashedPassword);

    if ($insertStmt->execute()) {
        echo json_encode(["success" => true, "message" => "Signup successful!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Signup failed. Please try again."]);
    }
} elseif ($action === "login") {
    // Login process
    $username = $data['username'];
    $password = $data['password'];

    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        echo json_encode(["success" => true, "message" => "Login successful", "user_id" => $user['id']]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid action."]);
}
?>
