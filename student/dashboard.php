<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
$user_id = $_SESSION['user_id'];
$teacher = $conn->query("SELECT * FROM teachers WHERE user_id = $user_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" style="width: 250px;">
            <h4>Teacher Panel</h4>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_attendance.php">Manage Attendance</a></li>
                <li class="nav-item"><a class="nav-link" href="enter_marks.php">Enter Marks</a></li>
                <li class="nav-item mt-5"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <h2>Welcome, <?php echo htmlspecialchars($teacher['first_name'] ?? 'Teacher'); ?>!</h2>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card p-3 bg-info text-white text-center">
                        <h5>Your Specialty</h5>
                        <h3><?php echo htmlspecialchars($teacher['subject_specialty'] ?? 'N/A'); ?></h3>
                    </div>
                </div>
            </div>
            
            <div class="card p-4 mt-4">
                <h5>Quick Instructions</h5>
                <ul>
                    <li>Use "Manage Attendance" to mark student attendance.</li>
                    <li>Use "Enter Marks" to add exam scores for students.</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
