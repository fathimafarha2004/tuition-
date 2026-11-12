<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

$edit_mode = isset($_GET['id']);
$fee = null;
$error = '';

// Get all students and classes for dropdowns
$students_result = $conn->query("SELECT id, student_id, name FROM students WHERE status='active' ORDER BY name");
$classes_result = $conn->query("SELECT id, class_name, subject, fee_amount FROM classes WHERE status='active' ORDER BY class_name");

// Store results in arrays to use multiple times
$students_array = [];
$classes_array = [];

if ($students_result) {
    while ($row = $students_result->fetch_assoc()) {
        $students_array[] = $row;
    }
}

if ($classes_result) {
    while ($row = $classes_result->fetch_assoc()) {
        $classes_array[] = $row;
    }
}

if ($edit_mode) {
    $id = (int)$_GET['id'];
    $result = $conn->query("SELECT * FROM fee_records WHERE id = $id");
    $fee = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = (int)$_POST['student_id'];
    $class_id = (int)$_POST['class_id'];
    $amount = (float)$_POST['amount'];
    $due_date = sanitize($_POST['due_date']);
    $status = sanitize($_POST['status']);
    $payment_method = sanitize($_POST['payment_method']);
    $notes = sanitize($_POST['notes']);
    $paid_date = ($status == 'paid' && !empty($_POST['paid_date'])) ? sanitize($_POST['paid_date']) : 'NULL';
    
    if ($edit_mode) {
        $id = (int)$_POST['id'];
        $query = "UPDATE fee_records SET 
                  student_id=$student_id, class_id=$class_id, amount=$amount,
                  due_date='$due_date', paid_date=" . ($paid_date == 'NULL' ? 'NULL' : "'$paid_date'") . ",
                  status='$status', payment_method='$payment_method', notes='$notes'
                  WHERE id=$id";
        
        if ($conn->query($query)) {
            $_SESSION['message'] = 'Fee record updated successfully!';
            redirect('fees.php');
        } else {
            $error = 'Error updating fee record: ' . $conn->error;
        }
    } else {
        $query = "INSERT INTO fee_records (student_id, class_id, amount, due_date, paid_date, status, payment_method, notes) 
                  VALUES ($student_id, $class_id, $amount, '$due_date', " . ($paid_date == 'NULL' ? 'NULL' : "'$paid_date'") . ", '$status', '$payment_method', '$notes')";
        
        if ($conn->query($query)) {
            $_SESSION['message'] = 'Fee record created successfully!';
            redirect('fees.php');
        } else {
            $error = 'Error creating fee record: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Fee Record - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1><?php echo $edit_mode ? 'Edit' : 'Add New'; ?> Fee Record</h1>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div style="padding: 2rem;">
                        <form method="POST" action="">
                            <?php if ($edit_mode): ?>
                                <input type="hidden" name="id" value="<?php echo $fee['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="student_id">Student *</label>
                                    <select id="student_id" name="student_id" required>
                                        <option value="">Select Student</option>
                                        <?php foreach ($students_array as $student): ?>
                                            <option value="<?php echo $student['id']; ?>" 
                                                <?php echo ($fee['student_id'] ?? '') == $student['id'] ? 'selected' : ''; ?>>
                                                <?php echo $student['student_id'] . ' - ' . $student['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="class_id">Class *</label>
                                    <select id="class_id" name="class_id" required onchange="updateAmount(this)">
                                        <option value="">Select Class</option>
                                        <?php foreach ($classes_array as $class): ?>
                                            <option value="<?php echo $class['id']; ?>" 
                                                data-amount="<?php echo $class['fee_amount']; ?>"
                                                <?php echo ($fee['class_id'] ?? '') == $class['id'] ? 'selected' : ''; ?>>
                                                <?php echo $class['class_name'] . ' - ' . $class['subject']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="amount">Amount (Rs.) *</label>
                                    <input type="number" step="0.01" id="amount" name="amount" 
                                           value="<?php echo $fee['amount'] ?? '0'; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="due_date">Due Date</label>
                                    <input type="date" id="due_date" name="due_date" 
                                           value="<?php echo $fee['due_date'] ?? ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="status">Status *</label>
                                    <select id="status" name="status" required onchange="togglePaidDate(this)">
                                        <option value="pending" <?php echo ($fee['status'] ?? 'pending') == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="paid" <?php echo ($fee['status'] ?? '') == 'paid' ? 'selected' : ''; ?>>Paid</option>
                                        <option value="overdue" <?php echo ($fee['status'] ?? '') == 'overdue' ? 'selected' : ''; ?>>Overdue</option>
                                    </select>
                                </div>
                                
                                <div class="form-group" id="paid_date_group" style="display: <?php echo ($fee['status'] ?? '') == 'paid' ? 'flex' : 'none'; ?>;">
                                    <label for="paid_date">Paid Date</label>
                                    <input type="date" id="paid_date" name="paid_date" 
                                           value="<?php echo $fee['paid_date'] ?? ''; ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label for="payment_method">Payment Method</label>
                                    <input type="text" id="payment_method" name="payment_method" 
                                           value="<?php echo $fee['payment_method'] ?? ''; ?>"
                                           placeholder="e.g., Cash, Bank Transfer">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="notes">Notes</label>
                                <textarea id="notes" name="notes"><?php echo $fee['notes'] ?? ''; ?></textarea>
                            </div>
                            
                            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                                <button type="submit" class="btn">
                                    <?php echo $edit_mode ? 'Update' : 'Create'; ?> Fee Record
                                </button>
                                <a href="fees.php" class="btn" style="background: #6c757d;">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function updateAmount(select) {
            const amount = select.options[select.selectedIndex].getAttribute('data-amount');
            if (amount) {
                document.getElementById('amount').value = amount;
            }
        }
        
        function togglePaidDate(select) {
            const paidDateGroup = document.getElementById('paid_date_group');
            if (select.value === 'paid') {
                paidDateGroup.style.display = 'flex';
                document.getElementById('paid_date').value = new Date().toISOString().split('T')[0];
            } else {
                paidDateGroup.style.display = 'none';
            }
        }
    </script>
</body>
</html>