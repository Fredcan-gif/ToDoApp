<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // Redirect to dashboard if already logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - To-Do List</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f5f5f5;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .login-container h2 {
            margin-bottom: 20px;
        }
        .input-field {
            width: 90%;
            padding: 10px;
            margin: 10px auto;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: block;
        }
        .login-btn {
            width: 100%;
            padding: 10px;
            background: #1f2937;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .login-btn:hover {
            background: #374151;
        }
        .signup-link {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Welcome Back</h2>
        <input type="text" id="username" class="input-field" placeholder="Username" required>
        <input type="password" id="password" class="input-field" placeholder="Password" required>
        <button class="login-btn" onclick="login()">Log In</button>
        <p class="signup-link">Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>

    <script>
        const API_URL = "http://localhost/todo_app/";

        async function login() {
            const username = document.getElementById("username").value;
            const password = document.getElementById("password").value;

            const response = await fetch(API_URL + "auth.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "login", username, password })
            });

            const result = await response.json();

            if (result.user_id) {
                document.cookie = `user_id=${result.user_id}; path=/`;
                window.location.href = "dashboard.php";
            } else {
                alert(result.error || "Invalid credentials");
            }
        }
    </script>
</body>
</html>
