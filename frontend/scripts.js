const API_URL = "http://localhost/todo_app/todo.php";// Adjust based on your server path

// User Sign-up
async function signup() {
    const username = document.getElementById("newUsername").value;
    const password = document.getElementById("newPassword").value;

    const response = await fetch(API_URL + "auth.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            action: "signup", // ✅ Added action field
            username,
            password
        })
    });

    const result = await response.json();
    alert(result.message);

    if (result.success) {
        window.location.href = "index.php"; // Redirect to login page
    }
}

// User Login
async function login() {
    const username = document.getElementById("username").value;
    const password = document.getElementById("password").value;

    const response = await fetch(API_URL + "auth.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            action: "login", // ✅ Added action field
            username,
            password
        })
    });
    async function addTask() {
        const user_id = localStorage.getItem("user_id");
        const task = document.getElementById("taskInput").value;
    
        const response = await fetch(`${API_URL}?action=add`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ user_id, task })
        });
    
        const result = await response.json();
        if (result.success) {
            fetchTasks(); // Refresh UI
        } else {
            alert(result.error);
        }
    }
    
    // 🟢 Fetch Tasks
    async function fetchTasks() {
        const user_id = localStorage.getItem("user_id");
        if (!user_id) return;
    
        const response = await fetch(`${API_URL}?action=get&user_id=${user_id}`);
        const result = await response.json();
    
        const taskList = document.getElementById("taskList");
        const finishedList = document.getElementById("finishedTasks");
        taskList.innerHTML = "";
        finishedList.innerHTML = "";
    
        result.tasks.forEach(task => {
            const li = document.createElement("li");
            li.innerHTML = `${task.task}
                <button onclick="markComplete(${task.id})">✔</button>
                <button onclick="deleteTask(${task.id})">❌</button>`;
    
            if (task.status === "completed") {
                li.classList.add("completed");
                finishedList.appendChild(li);
            } else {
                taskList.appendChild(li);
            }
        });
    }
    const result = await response.json();
    alert(result.message);

    if (result.success) {
        localStorage.setItem("user_id", result.user_id);
        window.location.href = "dashboard.php"; // Redirect to dashboard
    } else {
        alert("Login failed: " + result.message);
    }
}

// Logout
async function logout() {
    try {
        const response = await fetch("http://localhost/todo_app/logout.php", {
            method: "POST",
            credentials: "include", // Ensures cookies are sent
            headers: { "Content-Type": "application/json" }
        });

        const result = await response.json();
        console.log("Logout response:", result);

        if (result.success) {
            localStorage.removeItem("user_id"); // Remove local storage
            document.cookie = "PHPSESSID=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;"; // Delete session cookie
            window.location.href = "index.php"; // Redirect to login page
        } else {
            alert("Logout failed. Try again.");
        }
    } catch (error) {
        console.error("Logout error:", error);
        alert("Error logging out.");
    }
}


// Mark as Completed
// ✅ Mark Task as Completed
async function markComplete(task_id) {
    const response = await fetch(`${API_URL}?action=update`, {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ task_id })
    });

    const result = await response.json();
    if (result.success) {
        fetchTasks(); // Refresh UI
    } else {
        alert(result.error);
    }
}

// 🟢 Delete Task
async function deleteTask(task_id) {
    const response = await fetch(`${API_URL}?action=delete`, {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ task_id })
    });

    const result = await response.json();
    if (result.success) {
        fetchTasks(); // Refresh UI
    } else {
        alert(result.error);
    }
}

// Load Tasks when Dashboard is Opened
if (window.location.pathname.includes("dashboard.php")) {
    fetchTasks();
}
