<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// Get student info
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

if ($student_id == 0) {
    redirect('students.php');
}

$student = $conn->query("SELECT * FROM students WHERE id = $student_id")->fetch_assoc();

if (!$student) {
    redirect('students.php');
}

// Handle enrollment/unenrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['enroll'])) {
        $class_id = (int)$_POST['class_id'];
        
        // Check if already enrolled
        $check = $conn->query("SELECT id FROM student_classes WHERE student_id=$student_id AND class_id=$class_id");
        
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO student_classes (student_id, class_id) VALUES ($student_id, $class_id)");
            $_SESSION['message'] = 'Student enrolled successfully!';
        } else {
            $_SESSION['message'] = 'Student is already enrolled in this class!';
        }
    } elseif (isset($_POST['unenroll'])) {
        $class_id = (int)$_POST['class_id'];
        $conn->query("DELETE FROM student_classes WHERE student_id=$student_id AND class_id=$class_id");
        $_SESSION['message'] = 'Student unenrolled successfully!';
    }
    
    redirect("enrollments.php?student_id=$student_id");
}

// Get enrolled classes
$enrolled_result = $conn->query("
    SELECT c.*, sc.id as enrollment_id
    FROM classes c
    JOIN student_classes sc ON c.id = sc.class_id
    WHERE sc.student_id = $student_id
    ORDER BY c.class_name
");

// Check if query was successful
if (!$enrolled_result) {
    die("Error getting enrolled classes: " . $conn->error);
}
$enrolled = $enrolled_result;

// Get available classes (not enrolled)
$available_result = $conn->query("
    SELECT c.*
    FROM classes c
    WHERE c.status = 'active'
    AND c.id NOT IN (
        SELECT class_id FROM student_classes WHERE student_id = $student_id
    )
    ORDER BY c.class_name
");

// Check if query was successful
if (!$available_result) {
    die("Error getting available classes: " . $conn->error);
}
$available = $available_result;

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Enrollments - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h1>Manage Enrollments</h1>
                    <a href="students.php" class="btn btn-secondary">← Back to Students</a>
                </div>
                
                <div class="card" style="margin-bottom: 2rem;">
                    <div style="padding: 1.5rem; background: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
                        <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 0.5rem;">Student Information</h3>
                        <p style="color: var(--gray-600); margin: 0;">
                            <strong><?php echo $student['name']; ?></strong> (<?php echo $student['student_id']; ?>)
                        </p>
                    </div>
                </div>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <div class="dashboard-grid">
                    <!-- Enrolled Classes -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Enrolled Classes (<?php echo $enrolled->num_rows; ?>)</h2>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Schedule</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($enrolled->num_rows > 0): ?>
                                        <?php while ($class = $enrolled->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $class['class_name']; ?></td>
                                            <td><?php echo $class['subject']; ?></td>
                                            <td><?php echo $class['schedule'] ?: '-'; ?></td>
                                            <td>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                                                    <button type="submit" name="unenroll" class="btn-icon delete" 
                                                            onclick="return confirm('Unenroll from this class?')" title="Unenroll">
                                                        ❌
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 2rem;">No enrolled classes</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Available Classes -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Available Classes (<?php echo $available->num_rows; ?>)</h2>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Schedule</th>
                                        <th>Fee</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($available->num_rows > 0): ?>
                                        <?php while ($class = $available->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $class['class_name']; ?></td>
                                            <td><?php echo $class['subject']; ?></td>
                                            <td><?php echo $class['schedule'] ?: '-'; ?></td>
                                            <td><?php echo formatCurrency($class['fee_amount']); ?></td>
                                            <td>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="class_id" value="<?php echo $class['id']; ?>">
                                                    <button type="submit" name="enroll" class="btn-icon" 
                                                            style="background: var(--success-light);" title="Enroll">
                                                        ➕
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 2rem;">
                                                <?php if ($enrolled->num_rows > 0): ?>
                                                    All available classes enrolled!
                                                <?php else: ?>
                                                    No active classes available
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
