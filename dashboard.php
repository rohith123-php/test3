<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
$user_id = $_SESSION['user_id'];
$student = $conn->query("SELECT s.*, c.class_name, c.section FROM students s LEFT JOIN classes c ON s.class_id = c.id WHERE s.user_id = $user_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" style="width: 250px;">
            <h4>Student Panel</h4>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="view_profile.php">My Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="view_attendance.php">My Attendance</a></li>
                <li class="nav-item"><a class="nav-link" href="view_marks.php">My Marks</a></li>
                <li class="nav-item mt-5"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <h2>Welcome, <?php echo htmlspecialchars($student['first_name'] ?? 'Student'); ?>!</h2>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card p-3 bg-primary text-white text-center">
                        <h5>Your Class</h5>
                        <h3><?php echo htmlspecialchars(($student['class_name'] ?? 'N/A') . ' - ' . ($student['section'] ?? '')); ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
