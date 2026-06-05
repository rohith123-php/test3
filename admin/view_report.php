<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
// A simple overall report for demonstration
$attendance_report = $conn->query("
    SELECT c.class_name, c.section, COUNT(a.id) as total_records, 
    SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present_count
    FROM classes c
    LEFT JOIN attendance a ON c.id = a.class_id
    GROUP BY c.id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar p-3" style="width: 250px;">
            <h4>Admin Panel</h4><hr>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_students.php">Manage Students</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_teachers.php">Manage Teachers</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_classes.php">Classes & Subjects</a></li>
                <li class="nav-item"><a class="nav-link active" href="view_reports.php">Reports</a></li>
                <li class="nav-item mt-5"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
            </ul>
        </div>
        
        <div class="main-content flex-grow-1">
            <h2>School Reports</h2>
            
            <div class="card p-4 mt-4">
                <h5>Overall Attendance by Class</h5>
                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Total Attendance Records</th>
                            <th>Total Present</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $attendance_report->fetch_assoc()): 
                            $pct = $row['total_records'] > 0 ? round(($row['present_count'] / $row['total_records']) * 100, 2) : 0;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['class_name'] . ' ' . $row['section']); ?></td>
                            <td><?php echo $row['total_records']; ?></td>
                            <td><?php echo $row['present_count']; ?></td>
                            <td>
                                <div class="progress">
                                  <div class="progress-bar <?php echo $pct < 50 ? 'bg-danger' : 'bg-success'; ?>" role="progressbar" style="width: <?php echo $pct; ?>%;" aria-valuenow="<?php echo $pct; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $pct; ?>%</div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
