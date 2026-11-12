<?php
require_once '../config.php';

if (!isStudentLoggedIn()) {
    redirect('login.php');
}

$student_id = $_SESSION['student_id'];

$fees = $conn->query("
    SELECT f.*, c.class_name, c.subject 
    FROM fee_records f
    JOIN classes c ON f.class_id = c.id
    WHERE f.student_id = $student_id
    ORDER BY f.created_at DESC
");

// Calculate totals
$total_amount = 0;
$total_paid = 0;
$total_pending = 0;

$fees_temp = $conn->query("SELECT * FROM fee_records WHERE student_id = $student_id");
while ($fee = $fees_temp->fetch_assoc()) {
    $total_amount += $fee['amount'];
    if ($fee['status'] == 'paid') {
        $total_paid += $fee['amount'];
    } else {
        $total_pending += $fee['amount'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Status - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1>Fee Status</h1>
                
                <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 2rem;">
                    <div class="stat-card purple">
                        <div class="stat-icon">💵</div>
                        <div class="stat-info">
                            <h3><?php echo formatCurrency($total_amount); ?></h3>
                            <p>Total Amount</p>
                        </div>
                    </div>
                    
                    <div class="stat-card green">
                        <div class="stat-icon">✅</div>
                        <div class="stat-info">
                            <h3><?php echo formatCurrency($total_paid); ?></h3>
                            <p>Total Paid</p>
                        </div>
                    </div>
                    
                    <div class="stat-card orange">
                        <div class="stat-icon">⏰</div>
                        <div class="stat-info">
                            <h3><?php echo formatCurrency($total_pending); ?></h3>
                            <p>Total Pending</p>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Payment History</h2>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Class - Subject</th>
                                    <th>Amount</th>
                                    <th>Due Date</th>
                                    <th>Paid Date</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($fees->num_rows > 0): ?>
                                    <?php while ($fee = $fees->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo $fee['class_name'] . ' - ' . $fee['subject']; ?></strong></td>
                                        <td><?php echo formatCurrency($fee['amount']); ?></td>
                                        <td><?php echo $fee['due_date'] ? formatDate($fee['due_date']) : '-'; ?></td>
                                        <td><?php echo $fee['paid_date'] ? formatDate($fee['paid_date']) : '-'; ?></td>
                                        <td><?php echo $fee['payment_method'] ?: '-'; ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $fee['status']; ?>">
                                                <?php echo ucfirst($fee['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $fee['notes'] ?: '-'; ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 2rem;">
                                            No fee records found.
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
</body>
</html>