<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_class') {
    $class_name = $conn->real_escape_string($_POST['class_name']);
    $section = $conn->real_escape_string($_POST['section']);
    $conn->query("INSERT INTO classes (class_name, section) VALUES ('$class_name', '$section')");
    header('Location: manage_classes.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_subject') {
    $subject_name = $conn->real_escape_string($_POST['subject_name']);
    $class_id = (int)$_POST['class_id'];
    $conn->query("INSERT INTO subjects (subject_name, class_id) VALUES ('$subject_name', $class_id)");
    header('Location: manage_classes.php');
    exit;
}
$classes = $conn->query("SELECT * FROM classes");
$subjects = $conn->query("SELECT s.*, c.class_name, c.section FROM subjects s JOIN classes c ON s.class_id = c.id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Classes</title>
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
                <li class="nav-item"><a class="nav-link active" href="manage_classes.php">Classes & Subjects</a></li>
                <li class="nav-item"><a class="nav-link" href="view_reports.php">Reports</a></li>
                <li class="nav-item mt-5"><a class="nav-link text-danger" href="../logout.php">Logout</a></li>
            </ul>
        </div>
        
        <div class="main-content flex-grow-1">
            <h2>Manage Classes & Subjects</h2>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card p-4 mb-4">
                        <h5>Add New Class</h5>
                        <form method="POST">
                            <input type="hidden" name="action" value="add_class">
                            <div class="mb-3">
                                <input type="text" name="class_name" class="form-control" placeholder="Class Name (e.g. 10th)" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="section" class="form-control" placeholder="Section (e.g. A)" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Class</button>
                        </form>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-4 mb-4">
                        <h5>Add New Subject</h5>
                        <form method="POST">
                            <input type="hidden" name="action" value="add_subject">
                            <div class="mb-3">
                                <input type="text" name="subject_name" class="form-control" placeholder="Subject Name" required>
                            </div>
                            <div class="mb-3">
                                <select name="class_id" class="form-control" required>
                                    <option value="">Select Class</option>
                                    <?php 
                                    $classes_dd = $conn->query("SELECT * FROM classes");
                                    while($row = $classes_dd->fetch_assoc()): ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['class_name'] . ' - ' . $row['section']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Subject</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
