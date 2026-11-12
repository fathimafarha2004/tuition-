<?php
require_once '../config.php';

if (!isStudentLoggedIn()) {
    redirect('login.php');
}

$student_id = $_SESSION['student_id'];
$student = $conn->query("SELECT * FROM students WHERE id = $student_id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1>My Profile</h1>
                
                <div class="card">
                    <div style="padding: 2rem;">
                        <div style="text-align: center; margin-bottom: 2rem;">
                            <div style="width: 120px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 60px; color: white;">
                                👤
                            </div>
                            <h2 style="margin-bottom: 0.5rem;"><?php echo $student['name']; ?></h2>
                            <p style="color: #666;"><?php echo $student['student_id']; ?></p>
                            <span class="badge badge-<?php echo $student['status']; ?>" style="margin-top: 0.5rem;">
                                <?php echo ucfirst($student['status']); ?>
                            </span>
                        </div>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Student ID</label>
                                <input type="text" value="<?php echo $student['student_id']; ?>" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" value="<?php echo $student['name']; ?>" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" value="<?php echo $student['email']; ?>" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" value="<?php echo $student['phone'] ?: 'N/A'; ?>" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="text" value="<?php echo $student['date_of_birth'] ? formatDate($student['date_of_birth']) : 'N/A'; ?>" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label>Enrollment Date</label>
                                <input type="text" value="<?php echo formatDate($student['enrollment_date']); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Address</label>
                            <textarea readonly><?php echo $student['address'] ?: 'N/A'; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>