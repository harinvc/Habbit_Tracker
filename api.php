<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// Database Configuration
$host = 'localhost';
$dbname = 'habit_tracker';
$user = 'root'; // Change if your phpMyAdmin uses a different username
$pass = '';     // Change if your phpMyAdmin uses a password

// Connect to Database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed. Please check db credentials.']);
    exit;
}

// Read incoming JSON payload
$data = json_decode(file_get_contents("php://input"), true);
$action = $data['action'] ?? '';

// Helper function to return JSON
function sendResponse($success, $message, $extra = []) {
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

// API Routing
switch ($action) {
    case 'register':
        $username = trim($data['username']);
        $password = password_hash(trim($data['password']), PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) sendResponse(false, 'Username already exists.');

        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        if ($stmt->execute([$username, $password])) {
            sendResponse(true, 'Registration successful. You can now login.');
        }
        sendResponse(false, 'Registration failed.');
        break;

    case 'login':
        $username = trim($data['username']);
        $password = trim($data['password']);

        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            unset($user['password']); // Don't send password hash to frontend
            sendResponse(true, 'Login successful.', ['user' => $user]);
        }
        sendResponse(false, 'Invalid username or password.');
        break;

    case 'logout':
        session_destroy();
        sendResponse(true, 'Logged out.');
        break;

    case 'get_tasks':
        if (!isset($_SESSION['user_id'])) sendResponse(false, 'Unauthorized');
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        sendResponse(true, 'Tasks fetched', ['data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        break;

    case 'add_task':
        if (!isset($_SESSION['user_id'])) sendResponse(false, 'Unauthorized');
        $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $data['title'], $data['description'], $data['type']]);
        sendResponse(true, 'Task added successfully.');
        break;

    case 'update_task':
        if (!isset($_SESSION['user_id'])) sendResponse(false, 'Unauthorized');
        $stmt = $pdo->prepare("UPDATE tasks SET title = ?, description = ?, type = ?, status = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$data['title'], $data['description'], $data['type'], $data['status'], $data['id'], $_SESSION['user_id']]);
        sendResponse(true, 'Task updated.');
        break;

    case 'delete_task':
        if (!isset($_SESSION['user_id'])) sendResponse(false, 'Unauthorized');
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([$data['id'], $_SESSION['user_id']]);
        sendResponse(true, 'Task deleted.');
        break;

    // ADMIN FEATURES
    case 'get_admin_data':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') sendResponse(false, 'Unauthorized');
        
        $users = $pdo->query("SELECT id, username, role, created_at FROM users")->fetchAll(PDO::FETCH_ASSOC);
        $taskCount = $pdo->query("SELECT COUNT(*) as count FROM tasks")->fetch()['count'];
        
        sendResponse(true, 'Admin data fetched', [
            'users' => $users,
            'total_tasks' => $taskCount
        ]);
        break;

    case 'delete_user':
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') sendResponse(false, 'Unauthorized');
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$data['id']]);
        sendResponse(true, 'User deleted.');
        break;

    default:
        sendResponse(false, 'Invalid action.');
}
?>