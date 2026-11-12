<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$edit_mode = isset($_GET['id']);
$class = null;
$error = '';

if ($edit_mode) {
    $id = (int)$_GET['id'];
    $result = $conn->query("SELECT * FROM classes WHERE id = $id");
    $class = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_name = sanitize($_POST['class_name']);
    $subject = sanitize($_POST['subject']);
    $teacher_name = sanitize($_POST['teacher_name']);
    $schedule = sanitize($_POST['schedule']);
    $room_number = sanitize($_POST['room_number']);
    $fee_amount = (float)$_POST['fee_amount'];
    $status = sanitize($_POST['status']);
    
    if ($edit_mode) {
        $id = (int)$_POST['id'];
        $query = "UPDATE classes SET 
                  class_name='$class_name', subject='$subject', teacher_name='$teacher_name',
                  schedule='$schedule', room_number='$room_number', fee_amount=$fee_amount, status='$status'
                  WHERE id=$id";
        
        if ($conn->query($query)) {
            $_SESSION['message'] = 'Class updated successfully!';
            redirect('classes.php');
        } else {
            $error = 'Error updating class: ' . $conn->error;
        }
    } else {
        $query = "INSERT INTO classes (class_name, subject, teacher_name, schedule, room_number, fee_amount, status) 
                  VALUES ('$class_name', '$subject', '$teacher_name', '$schedule', '$room_number', $fee_amount, '$status')";
        
        if ($conn->query($query)) {
            $_SESSION['message'] = 'Class created successfully!';
            redirect('classes.php');
        } else {
            $error = 'Error creating class: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Class - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1><?php echo $edit_mode ? 'Edit' : 'Add New'; ?> Class</h1>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div style="padding: 2rem;">
                        <form method="POST" action="">
                            <?php if ($edit_mode): ?>
                                <input type="hidden" name="id" value="<?php echo $class['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="class_name">Class Name *</label>
                                    <input type="text" id="class_name" name="class_name" 
                                           value="<?php echo $class['class_name'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="subject">Subject *</label>
                                    <input type="text" id="subject" name="subject" 
                                           value="<?php echo $class['subject'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="teacher_name">Teacher Name</label>
                                    <input type="text" id="teacher_name" name="teacher_name" 
                                           value="<?php echo $class['teacher_name'] ?? ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="schedule">Schedule</label>
                                    <input type="text" id="schedule" name="schedule" 
                                           value="<?php echo $class['schedule'] ?? ''; ?>"
                                           placeholder="e.g., Mon/Wed 3:00 PM">
                                </div>
                                
                                <div class="form-group">
                                    <label for="room_number">Room Number</label>
                                    <input type="text" id="room_number" name="room_number" 
                                           value="<?php echo $class['room_number'] ?? ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="fee_amount">Fee Amount (Rs.) *</label>
                                    <input type="number" step="0.01" id="fee_amount" name="fee_amount" 
                                           value="<?php echo $class['fee_amount'] ?? '0'; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select id="status" name="status" required>
                                        <option value="active" <?php echo ($class['status'] ?? 'active') == 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($class['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                                <button type="submit" class="btn">
                                    <?php echo $edit_mode ? 'Update' : 'Create'; ?> Class
                                </button>
                                <a href="classes.php" class="btn" style="background: #6c757d;">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>