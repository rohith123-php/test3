<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_marks') {
    $subject_id = (int)$_POST['subject_id'];
    $exam_type = $conn->real_escape_string($_POST['exam_type']);
    $max_marks = (float)$_POST['max_marks'];
    
    foreach ($_POST['marks'] as $student_id => $marks_obtained) {
        if ($marks_obtained === '') continue; // Skip empty
        $student_id = (int)$student_id;
        $marks_obtained = (float)$marks_obtained;
        
        $conn->query("INSERT INTO marks (student_id, subject_id, marks_obtained, max_marks, exam_type) VALUES ($student_id, $subject_id, $marks_obtained, $max_marks, '$exam_type')");
    }
    $success_msg = "Marks saved successfully!";
}
$classes = $conn->query("SELECT * FROM classes");
$selected_class = isset($_GET['class_id']) ? (int)$_GET['class_id'] : null;
if ($selected_class) {
    $students = $conn->query("SELECT * FROM students WHERE class_id = $selected_class");
    $subjects = $conn->query("SELECT * FROM subjects WHERE class_id = $selected_class");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enter Marks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="d-flex">
        <div class="sidebar p-3" style="width: 250px;">
            <h4>Teacher Panel</h4><hr>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_attendance.php">Manage Attendance</a></li>
                <li class="nav-item"><a class="nav-link active" href="enter_marks.php">Enter Marks</a></li>
                <li class="nav-item mt-5"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
            </ul>
        </div>
        
        <div class="main-content flex-grow-1">
            <h2>Enter Marks</h2>
            
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
            <?php if($selected_class && $students->num_rows > 0 && $subjects->num_rows > 0): ?>
            <div class="card p-4">
                <form method="POST">
                    <input type="hidden" name="action" value="save_marks">
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Subject</label>
                            <select name="subject_id" class="form-control" required>
                                <?php while($sub = $subjects->fetch_assoc()): ?>
                                    <option value="<?php echo $sub['id']; ?>"><?php echo $sub['subject_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Exam Type</label>
                            <input type="text" name="exam_type" class="form-control" placeholder="e.g. Midterm, Final" required>
                        </div>
                        <div class="col-md-4">
                            <label>Max Marks</label>
                            <input type="number" name="max_marks" class="form-control" value="100" required>
                        </div>
                    </div>
                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Marks Obtained</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($s = $students->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></td>
                                <td>
                                    <input type="number" step="0.01" name="marks[<?php echo $s['id']; ?>]" class="form-control" placeholder="Enter marks">
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary">Save Marks</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
