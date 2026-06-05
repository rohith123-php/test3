<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
$user_id = $_SESSION['user_id'];
$student = $conn->query("SELECT s.*, c.class_name, c.section, u.username FROM students s LEFT JOIN classes c ON s.class_id = c.id LEFT JOIN users u ON s.user_id = u.id WHERE s.user_id = $user_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" style="width: 250px;">
            <h4>Student Panel</h4><hr>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="view_profile.php">My Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="view_attendance.php">My Attendance</a></li>
                <li class="nav-item"><a class="nav-link" href="view_marks.php">My Marks</a></li>
                <li class="nav-item mt-5"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <h2>My Profile</h2>
            <div class="card p-4 mt-4" style="max-width: 600px;">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Name:</th>
                        <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Username:</th>
                        <td><?php echo htmlspecialchars($student['username']); ?></td>
                    </tr>
                    <tr>
                        <th>Class:</th>
                        <td><?php echo htmlspecialchars(($student['class_name'] ?? 'N/A') . ' - ' . ($student['section'] ?? '')); ?></td>
                    </tr>
                    <tr>
                        <th>Contact:</th>
                        <td><?php echo htmlspecialchars($student['contact_info'] ?? 'Not provided'); ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
