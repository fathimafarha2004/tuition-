<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$edit_mode = isset($_GET['id']);
$student = null;
$error = '';

if ($edit_mode) {
    $id = (int)$_GET['id'];
    $result = $conn->query("SELECT * FROM students WHERE id = $id");
    $student = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = sanitize($_POST['student_id']);
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $address = sanitize($_POST['address']);
    $status = sanitize($_POST['status']);
    
    // Handle password
    $password_update = '';
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_update = ", password='$password'";
    }
    
    if ($edit_mode) {
        $id = (int)$_POST['id'];
        $query = "UPDATE students SET 
                  student_id='$student_id', name='$name', email='$email',
                  phone='$phone', address='$address', status='$status'$password_update
                  WHERE id=$id";
        
        if ($conn->query($query)) {
            $_SESSION['message'] = 'Student updated successfully!';
            redirect('students.php');
        } else {
            $error = 'Error updating student: ' . $conn->error;
        }
    } else {
        // Check if student_id or email already exists
        $check = $conn->query("SELECT id FROM students WHERE student_id='$student_id' OR email='$email'");
        if ($check->num_rows > 0) {
            $error = 'Student ID or Email already exists!';
        } else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $query = "INSERT INTO students (student_id, name, email, phone, address, password, status) 
                      VALUES ('$student_id', '$name', '$email', '$phone', '$address', '$password', '$status')";
            
            if ($conn->query($query)) {
                $_SESSION['message'] = 'Student created successfully!';
                redirect('students.php');
            } else {
                $error = 'Error creating student: ' . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Student - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1><?php echo $edit_mode ? 'Edit' : 'Add New'; ?> Student</h1>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div style="padding: 2rem;">
                        <form method="POST" action="">
                            <?php if ($edit_mode): ?>
                                <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="student_id">Student ID *</label>
                                    <input type="text" id="student_id" name="student_id" 
                                           value="<?php echo $student['student_id'] ?? ''; ?>" 
                                           <?php echo $edit_mode ? 'readonly' : ''; ?> required>
                                    <?php if (!$edit_mode): ?>
                                        <small style="color: #666; font-size: 0.85rem;">e.g., ST20255571</small>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="name">Full Name *</label>
                                    <input type="text" id="name" name="name" 
                                           value="<?php echo $student['name'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email Address *</label>
                                    <input type="email" id="email" name="email" 
                                           value="<?php echo $student['email'] ?? ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" id="phone" name="phone" 
                                           value="<?php echo $student['phone'] ?? ''; ?>">
                                </div>
                                
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label for="address">Address</label>
                                    <textarea id="address" name="address" rows="3"><?php echo $student['address'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password">Password <?php echo $edit_mode ? '' : '*'; ?></label>
                                    <input type="password" id="password" name="password" 
                                           <?php echo $edit_mode ? '' : 'required'; ?>>
                                    <?php if ($edit_mode): ?>
                                        <small style="color: #666; font-size: 0.85rem;">Leave blank to keep current password</small>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select id="status" name="status" required>
                                        <option value="active" <?php echo ($student['status'] ?? 'active') == 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($student['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                                <button type="submit" class="btn">
                                    <?php echo $edit_mode ? 'Update' : 'Create'; ?> Student
                                </button>
                                <a href="students.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
