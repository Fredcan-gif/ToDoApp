<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // Redirect to dashboard if logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - To-Do List</title>
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
        .signup-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }
        .signup-container h2 {
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
        .signup-btn {
            width: 100%;
            padding: 10px;
            background: #1f2937;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .signup-btn:hover {
            background: #374151;
        }
        .signin-link {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Sign Up</h2>
        <input type="text" id="newUsername" class="input-field" placeholder="Username" required>
        <input type="password" id="newPassword" class="input-field" placeholder="Password" required>
        <button class="signup-btn" onclick="signup()">Sign Up</button>
        <p class="signin-link">Already have an account? <a href="index.php">Login here</a></p>
    </div>
    
    <script>
        const API_URL = "http://localhost/todo_app/";

        async function signup() {
            const username = document.getElementById("newUsername").value;
            const password = document.getElementById("newPassword").value;

            const response = await fetch(API_URL + "auth.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "signup", username, password })
            });

            const result = await response.json();
            if (result.message) {
                alert(result.message);
                window.location.href = "index.php"; // Redirect to login after signup
            } else {
                alert(result.error || "Signup failed");
            }
        }
    </script>
</body>
</html>
