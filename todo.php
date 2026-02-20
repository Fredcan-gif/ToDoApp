<?php
include 'db.php';
header("Content-Type: application/json");

// Enable error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

// Get JSON input data
$data = json_decode(file_get_contents("php://input"), true);

// Determine action
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Handle adding a task
if ($method === 'POST' && $action === 'add') {
    $user_id = $data['user_id'] ?? null;
    $task = $data['task'] ?? null;

    if (!$user_id || !$task) {
        echo json_encode(["error" => "Missing user_id or task"]);
        exit;
    }

    $sql = "INSERT INTO tasks (user_id, task, status) VALUES (:user_id, :task, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':task', $task);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Task added successfully"]);
    } else {
        echo json_encode(["error" => "Failed to add task"]);
    }
    exit;
}

// Handle fetching tasks
if ($method === 'GET' && $action === 'get') {
    $user_id = $_GET['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode(["error" => "Missing user_id"]);
        exit;
    }

    $sql = "SELECT id, task, status FROM tasks WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["tasks" => $tasks]);
    exit;
}

// Handle marking a task as completed
if ($method === 'PUT' && $action === 'update') {
    $data = json_decode(file_get_contents("php://input"), true);
    $task_id = $data['task_id'] ?? null;

    if (!$task_id) {
        echo json_encode(["error" => "Missing task_id"]);
        exit;
    }

    // Explicitly update the status to 'completed'
    $sql = "UPDATE tasks SET status = 'finished' WHERE id = :task_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':task_id', $task_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // 🔹 Debugging: Check if the update worked
        $checkSql = "SELECT status FROM tasks WHERE id = :task_id";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(':task_id', $task_id, PDO::PARAM_INT);
        $checkStmt->execute();
        $updatedTask = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($updatedTask) {
            echo json_encode(["success" => true, "message" => "Task marked as completed", "status" => $updatedTask["status"]]);
        } else {
            echo json_encode(["error" => "Failed to verify update"]);
        }
    } else {
        // 🔹 Debugging: Log any SQL errors
        $errorInfo = $stmt->errorInfo();
        echo json_encode(["error" => "Failed to update task", "sqlError" => $errorInfo]);
    }
    exit;
}

// Handle editing a task
if ($method === 'PUT' && $action === 'edit') {
    $task_id = $data['task_id'] ?? null;
    $updated_task = $data['task'] ?? null;

    if (!$task_id || !$updated_task) {
        echo json_encode(["error" => "Missing task_id or updated task"]);
        exit;
    }

    $sql = "UPDATE tasks SET task = :updated_task WHERE id = :task_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':updated_task', $updated_task);
    $stmt->bindParam(':task_id', $task_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Task updated successfully"]);
    } else {
        echo json_encode(["error" => "Failed to update task"]);
    }
    exit;
}



// Handle deleting a task
if ($method === 'DELETE' && $action === 'delete') {
    $task_id = $data['task_id'] ?? null;

    if (!$task_id) {
        echo json_encode(["error" => "Missing task_id"]);
        exit;
    }

    $sql = "DELETE FROM tasks WHERE id = :task_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':task_id', $task_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Task deleted"]);
    } else {
        echo json_encode(["error" => "Failed to delete task"]);
    }
    exit;
}

// If no valid action found
echo json_encode(["error" => "Invalid action"]);
exit;
?>
