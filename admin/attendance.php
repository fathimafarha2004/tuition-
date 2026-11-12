<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// Handle mark attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['mark_attendance'])) {
    $class_id = (int)$_POST['class_id'];
    $date = sanitize($_POST['date']);
    
    foreach ($_POST['attendance'] as $student_id => $status) {
        $student_id = (int)$student_id;
        $status = sanitize($status);
        
        // Check if record exists
        $check = $conn->query("SELECT id FROM attendance WHERE student_id=$student_id AND class_id=$class_id AND date='$date'");
        
        if ($check->num_rows > 0) {
            // Update existing
            $conn->query("UPDATE attendance SET status='$status' WHERE student_id=$student_id AND class_id=$class_id AND date='$date'");
        } else {
            // Insert new
            $conn->query("INSERT INTO attendance (student_id, class_id, date, status) VALUES ($student_id, $class_id, '$date', '$status')");
        }
    }
    
    $_SESSION['message'] = 'Attendance marked successfully!';
    redirect('attendance.php');
}

// Get classes for dropdown
$classes_result = $conn->query("SELECT id, class_name, subject FROM classes WHERE status='active' ORDER BY class_name");
$classes_array = [];
if ($classes_result) {
    while ($row = $classes_result->fetch_assoc()) {
        $classes_array[] = $row;
    }
}

// If class selected, get students
$selected_class = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$selected_date = isset($_GET['date']) ? sanitize($_GET['date']) : date('Y-m-d');

$students = null;
$attendance_records = [];

if ($selected_class > 0) {
    // Get enrolled students
    $students_result = $conn->query("
        SELECT s.id, s.student_id, s.name 
        FROM students s
        JOIN student_classes sc ON s.id = sc.student_id
        WHERE sc.class_id = $selected_class AND s.status='active'
        ORDER BY s.name
    ");
    
    // Check if query returned results
    if (!$students_result) {
        $students = null;
    } else {
        $students = $students_result;
    }
    
    // Get existing attendance for this date
    $existing = $conn->query("
        SELECT student_id, status 
        FROM attendance 
        WHERE class_id = $selected_class AND date = '$selected_date'
    ");
    
    while ($row = $existing->fetch_assoc()) {
        $attendance_records[$row['student_id']] = $row['status'];
    }
}

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1>Attendance Management</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div style="padding: 2rem;">
                        <form method="GET" action="">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="class_id">Select Class *</label>
                                    <select id="class_id" name="class_id" required onchange="this.form.submit()">
                                        <option value="">-- Select Class --</option>
                                        <?php foreach ($classes_array as $class): ?>
                                            <option value="<?php echo $class['id']; ?>" 
                                                <?php echo $selected_class == $class['id'] ? 'selected' : ''; ?>>
                                                <?php echo $class['class_name'] . ' - ' . $class['subject']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="date">Date *</label>
                                    <input type="date" id="date" name="date" value="<?php echo $selected_date; ?>" 
                                           required onchange="this.form.submit()">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <?php if ($selected_class > 0 && $students && $students->num_rows > 0): ?>
                <div class="card" style="margin-top: 2rem;">
                    <div class="card-header">
                        <h2>Mark Attendance</h2>
                    </div>
                    <div style="padding: 2rem;">
                        <form method="POST" action="">
                            <input type="hidden" name="mark_attendance" value="1">
                            <input type="hidden" name="class_id" value="<?php echo $selected_class; ?>">
                            <input type="hidden" name="date" value="<?php echo $selected_date; ?>">
                            
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Present</th>
                                            <th>Absent</th>
                                            <th>Late</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($student = $students->fetch_assoc()): 
                                            $current_status = $attendance_records[$student['id']] ?? 'present';
                                        ?>
                                        <tr>
                                            <td><?php echo $student['student_id']; ?></td>
                                            <td><?php echo $student['name']; ?></td>
                                            <td>
                                                <input type="radio" name="attendance[<?php echo $student['id']; ?>]" 
                                                       value="present" <?php echo $current_status == 'present' ? 'checked' : ''; ?>>
                                            </td>
                                            <td>
                                                <input type="radio" name="attendance[<?php echo $student['id']; ?>]" 
                                                       value="absent" <?php echo $current_status == 'absent' ? 'checked' : ''; ?>>
                                            </td>
                                            <td>
                                                <input type="radio" name="attendance[<?php echo $student['id']; ?>]" 
                                                       value="late" <?php echo $current_status == 'late' ? 'checked' : ''; ?>>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div style="margin-top: 2rem;">
                                <button type="submit" class="btn">Save Attendance</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php elseif ($selected_class > 0): ?>
                <div class="card" style="margin-top: 2rem;">
                    <div style="padding: 2rem; text-align: center;">
                        <p style="color: #666;">No students enrolled in this class yet.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>