<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM fee_records WHERE id = $id");
    $_SESSION['message'] = 'Fee record deleted successfully!';
    redirect('fees.php');
}

// Handle mark as paid
if (isset($_GET['mark_paid'])) {
    $id = (int)$_GET['mark_paid'];
    $conn->query("UPDATE fee_records SET status='paid', paid_date=CURDATE() WHERE id=$id");
    $_SESSION['message'] = 'Fee marked as paid!';
    redirect('fees.php');
}

$filter = isset($_GET['filter']) ? sanitize($_GET['filter']) : '';
$where = $filter ? "WHERE f.status = '$filter'" : '';

$fees = $conn->query("
    SELECT f.*, s.name as student_name, s.student_id, c.class_name, c.subject 
    FROM fee_records f
    JOIN students s ON f.student_id = s.id
    JOIN classes c ON f.class_id = c.id
    $where
    ORDER BY f.created_at DESC
");

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Records - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1>Fee Records Management</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <div class="actions">
                    <div style="display: flex; gap: 1rem;">
                        <a href="?filter=" class="btn-small <?php echo $filter == '' ? 'active' : ''; ?>">All</a>
                        <a href="?filter=pending" class="btn-small <?php echo $filter == 'pending' ? 'active' : ''; ?>">Pending</a>
                        <a href="?filter=paid" class="btn-small <?php echo $filter == 'paid' ? 'active' : ''; ?>">Paid</a>
                        <a href="?filter=overdue" class="btn-small <?php echo $filter == 'overdue' ? 'active' : ''; ?>">Overdue</a>
                    </div>
                    <a href="fee-form.php" class="btn">+ Add Fee Record</a>
                </div>
                
                <div class="card">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Class - Subject</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Paid Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($fees->num_rows > 0): ?>
                                    <?php while ($fee = $fees->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $fee['student_id']; ?></td>
                                        <td><?php echo $fee['student_name']; ?></td>
                                        <td><?php echo $fee['class_name'] . ' - ' . $fee['subject']; ?></td>
                                        <td><?php echo formatCurrency($fee['amount']); ?></td>
                                        <td><?php echo $fee['due_date'] ? formatDate($fee['due_date']) : '-'; ?></td>
                                        <td><?php echo $fee['paid_date'] ? formatDate($fee['paid_date']) : '-'; ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $fee['status']; ?>">
                                                <?php echo ucfirst($fee['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <?php if ($fee['status'] != 'paid'): ?>
                                                    <a href="?mark_paid=<?php echo $fee['id']; ?>" class="btn-icon" title="Mark as Paid" onclick="return confirm('Mark as paid?')">✅</a>
                                                <?php endif; ?>
                                                <a href="fee-form.php?id=<?php echo $fee['id']; ?>" class="btn-icon edit" title="Edit">✏️</a>
                                                <a href="?delete=<?php echo $fee['id']; ?>" class="btn-icon delete" title="Delete" onclick="return confirm('Delete this record?')">🗑️</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 2rem;">No fee records found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>