?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
$user_id = $_SESSION['user_id'];
$student = $conn->query("SELECT id FROM students WHERE user_id = $user_id")->fetch_assoc();
$student_id = $student['id'] ?? 0;
$marks = $conn->query("
    SELECT m.*, s.subject_name 
    FROM marks m 
    JOIN subjects s ON m.subject_id = s.id 
    WHERE m.student_id = $student_id 
    ORDER BY m.exam_type, s.subject_name
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Marks</title>
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
                <li class="nav-item"><a class="nav-link" href="view_profile.php">My Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="view_attendance.php">My Attendance</a></li>
                <li class="nav-item"><a class="nav-link active" href="view_marks.php">My Marks</a></li>
                <li class="nav-item mt-5"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content flex-grow-1">
            <h2>My Marks</h2>
            <div class="card p-4 mt-4">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Exam Type</th>
                            <th>Subject</th>
                            <th>Marks Obtained</th>
                            <th>Max Marks</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($marks->num_rows > 0): ?>
                            <?php while($row = $marks->fetch_assoc()): 
                                $pct = ($row['max_marks'] > 0) ? round(($row['marks_obtained'] / $row['max_marks']) * 100, 2) : 0;
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['exam_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['marks_obtained']); ?></td>
                                <td><?php echo htmlspecialchars($row['max_marks']); ?></td>
                                <td><?php echo $pct; ?>%</td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No marks records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
