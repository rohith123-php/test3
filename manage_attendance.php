<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
$classes = $conn->query("SELECT * FROM classes");
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'mark_attendance') {
    $class_id = (int)$_POST['class_id'];
    $date = $conn->real_escape_string($_POST['attendance_date']);
    
    foreach ($_POST['status'] as $student_id => $status) {
        $student_id = (int)$student_id;
        $status = $conn->real_escape_string($status);
        
        // Check if exists
        $check = $conn->query("SELECT id FROM attendance WHERE student_id = $student_id AND class_id = $class_id AND attendance_date = '$date'");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE attendance SET status = '$status' WHERE student_id = $student_id AND class_id = $class_id AND attendance_date = '$date'");
        } else {
            $conn->query("INSERT INTO attendance (student_id, class_id, attendance_date, status) VALUES ($student_id, $class_id, '$date', '$status')");
        }
    }
    $success_msg = "Attendance saved successfully for $date!";
}
$selected_class = isset($_GET['class_id']) ? (int)$_GET['class_id'] : null;
if ($selected_class) {
    $students = $conn->query("SELECT * FROM students WHERE class_id = $selected_class");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar p-3" style="width: 250px;">
            <h4>Teacher Panel</h4><hr>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="manage_attendance.php">Manage Attendance</a></li>
                <li class="nav-item"><a class="nav-link" href="enter_marks.php">Enter Marks</a></li>
                <li class="nav-item mt-5"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
            </ul>
        </div>
        
        <div class="main-content flex-grow-1">
            <h2>Manage Attendance</h2>
            
            <?php if(isset($success_msg)): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            <div class="card p-4 mb-4">
                <form method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label>Select Class</label>
                            <select name="class_id" class="form-control" required onchange="this.form.submit()">
                                <option value="">-- Select --</option>
                                <?php while($row = $classes->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo ($selected_class == $row['id']) ? 'selected' : ''; ?>>
                                        <?php echo $row['class_name'] . ' - ' . $row['section']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <?php if($selected_class && $students->num_rows > 0): ?>
            <div class="card p-4">
                <form method="POST">
                    <input type="hidden" name="action" value="mark_attendance">
                    <input type="hidden" name="class_id" value="<?php echo $selected_class; ?>">
                    
                    <div class="mb-3 w-25">
                        <label>Date</label>
                        <input type="date" name="attendance_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($s = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></td>
                                <td>
                                    <select name="status[<?php echo $s['id']; ?>]" class="form-control">
                                        <option value="Present">Present</option>
                                        <option value="Absent">Absent</option>
                                    </select>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary">Save Attendance</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
