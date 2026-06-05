<?php
session_start();
require_once 'config/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($query);
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            if ($user['role'] == 'admin') {
                header('Location: admin/dashboard.php');
            } elseif ($user['role'] == 'teacher') {
                header('Location: teacher/dashboard.php');
            } elseif ($user['role'] == 'student') {
                header('Location: student/dashboard.php');
            }
            exit;
        } else {
            $_SESSION['error'] = "Invalid password.";
            header('Location: index.php');
            exit;
        }
    } else {
        $_SESSION['error'] = "User not found.";
        header('Location: index.php');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>
