<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ToDo</title>
    <link rel="stylesheet" href="styles.css">

    <script>
        const API_URL = "http://localhost/todo_app/";

        async function fetchTasks() {
            console.log("Fetching tasks...");
            const user_id = <?php echo json_encode($_SESSION['user_id']); ?>;
            if (!user_id) {
                console.log("No user ID found.");
                return;
            }

            try {
                const response = await fetch(`${API_URL}todo.php?action=get&user_id=${user_id}`);
                const result = await response.json();
                console.log("Received tasks:", result);

                const taskList = document.getElementById("taskList");
                const finishedList = document.getElementById("completedTasks");

                // Clear previous tasks
                taskList.innerHTML = "";
                finishedList.innerHTML = "";

                if (result.tasks) {
                    console.log("Rendering tasks...");
                    result.tasks.forEach(task => {
                        const li = document.createElement("li");
                        li.innerHTML = `
                            <div class="task-card">
                                <p id="task-text-${task.id}">${task.task}</p>
                                <div class="actions">
                                    ${task.status.trim().toLowerCase() === "finished" ? "" : `
                                    <button class="check-btn" onclick="markComplete(${task.id})">✔</button>`}
                                    <button class="edit-btn" onclick="editTask(${task.id})">✏️</button>
                                    <button class="delete-btn" onclick="deleteTask(${task.id})">✖</button>
                                </div>
                            </div>
                        `;

                        if (task.status.trim().toLowerCase() === "finished") {
                            console.log("Moving to Completed Tasks:", task);
                            li.classList.add("completed");
                            finishedList.appendChild(li);
                        } else {
                            console.log("Moving to Active Tasks:", task);
                            taskList.appendChild(li);
                        }
                    });
                } else {
                    console.log("Error fetching tasks:", result.error || "Unknown error");
                }
            } catch (error) {
                console.error("Fetch tasks error:", error);
            }
        }

        async function addTask() {
            console.log("Adding task...");

            const user_id = <?php echo json_encode($_SESSION['user_id']); ?>;
            const task = document.getElementById("taskTitle").value.trim();

            if (!user_id || !task) {
                alert("Please enter a task!");
                return;
            }

            try {
                const response = await fetch(API_URL + "todo.php?action=add", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ user_id, task })
                });

                const result = await response.json();
                console.log("Task add result:", result);

                if (result.success) {
                    document.getElementById("taskTitle").value = "";
                    fetchTasks();
                } else {
                    alert(result.error || "Error adding task.");
                }
            } catch (error) {
                console.error("Error adding task:", error);
            }
        }

        async function editTask(task_id) {
            const taskTextElement = document.getElementById(`task-text-${task_id}`);
            if (!taskTextElement) {
                console.error(`Task text not found for ID: ${task_id}`);
                return;
            }

            const newTask = prompt("Edit your task:", taskTextElement.innerText);
            if (!newTask) return;

            console.log(`Updating task ${task_id} to:`, newTask);

            try {
                const response = await fetch(`${API_URL}todo.php?action=edit`, {
                    method: "PUT",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ task_id, task: newTask })
                });

                const result = await response.json();
                console.log("Edit Task Result:", result);

                if (result.success) {
                    taskTextElement.innerText = newTask;
                    console.log("Updated task in UI.");
                } else {
                    alert("Failed to update task.");
                }
            } catch (error) {
                console.error("Edit task error:", error);
            }
        }

        async function deleteTask(task_id) {
            console.log("Deleting task:", task_id);

            try {
                const response = await fetch(API_URL + "todo.php?action=delete", {
                    method: "DELETE",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ task_id })
                });

                const result = await response.json();
                console.log("Delete task result:", result);

                if (result.success) {
                    fetchTasks();
                } else {
                    alert(result.error || "Error deleting task.");
                }
            } catch (error) {
                console.error("Error deleting task:", error);
            }
        }

        async function markComplete(task_id) {
            console.log("Marking complete... Task ID:", task_id);

            try {
                const response = await fetch(`${API_URL}todo.php?action=update`, {
                    method: "PUT",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ task_id })
                });

                const result = await response.json();
                console.log("Mark Complete Result:", result);

                if (result.success) {
                    fetchTasks();
                } else {
                    alert(result.error || "Error marking task as complete.");
                }
            } catch (error) {
                console.error("Error marking complete:", error);
            }
        }

        async function logout() {
            console.log("🔹 Logout function triggered!");
            try {
                console.log("🔹 Logging out...");

                const response = await fetch(API_URL + "logout.php", {
                    method: "POST",
                    credentials: "include",
                    headers: { "Content-Type": "application/json" }
                });

                console.log("🔹 Logout request sent...");
                const result = await response.json();
                console.log("🔹 Logout Response:", result);

                if (result.success) {
                    console.log("✅ Logout successful!");

                    localStorage.removeItem("user_id");
                    document.cookie = "PHPSESSID=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

                    window.location.href = "index.php";
                } else {
                    alert("❌ Logout failed. Please try again.");
                }
            } catch (error) {
                console.error("❌ Logout error:", error);
                alert("Error logging out.");
            }
        }

        window.onload = fetchTasks;
    </script>

    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 250px;
            background-color: #1e3a56;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .menu a {
            display: flex;
            background-color: #24344d;
            padding: 10px;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.3s;
        }

        .menu a:hover {
            background-color: #2c4b69;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f4f6f8;
        }

        .task-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }

        .task-column {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .task-card {
            background: white;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            position: relative;
}

        .task-card h3 {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
}

        .task-card p {
            font-size: 14px;
            color: #6b7280;
            margin: 8px 0;
}

        .task-card .timestamp {
            font-size: 12px;
            color: #a0aec0;
            margin-top: auto;
}

        .task-card .menu {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 18px;
            color: #6b7280;
}

        .actions {
            display: flex;
            gap: 10px;
        }

        .check-btn, .delete-btn, .edit-btn {
            border: none;
            cursor: pointer;
            padding: 5px 10px;
        }

        .check-btn {
            background: green;
            color: white;
        }

        .delete-btn {
            background: red;
            color: white;
        }

        .logout-btn {
            margin-top: auto;
            background: red;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        
    </style>
</head>
<body>

    <div class="sidebar">
        <h2>ToDo App</h2>
        <div class="menu">
            <a href="#">Dashboard</a>
        </div>
        <button class="logout-btn" onclick="logout()">🚪 Logout</button>
    </div>

    <div class="main-content">
        <h1>My Notes</h1>
        <div class="task-container">
            <div class="task-column">
                <h3>To Start</h3>
                <input type="text" id="taskTitle" placeholder="Task">
                <button onclick="addTask()">Add Task</button>
                <ul id="taskList"></ul>
            </div>

            <div class="task-column">
                <h3>Completed</h3>
                <ul id="completedTasks"></ul>
            </div>
        </div>
    </div>

</body>
</html>
