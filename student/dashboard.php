<?php
require_once '../config.php';

if (!isStudentLoggedIn()) {
    redirect('login.php');
}

$student_id = $_SESSION['student_id'];

// Get student info
$student = $conn->query("SELECT * FROM students WHERE id = $student_id")->fetch_assoc();

// Get enrolled classes count
$classes_count = $conn->query("SELECT COUNT(*) as count FROM student_classes WHERE student_id = $student_id")->fetch_assoc()['count'];

// Get pending fees count
$pending_fees = $conn->query("SELECT COUNT(*) as count FROM fee_records WHERE student_id = $student_id AND status='pending'")->fetch_assoc()['count'];

// Get total paid amount
$total_paid = $conn->query("SELECT SUM(amount) as total FROM fee_records WHERE student_id = $student_id AND status='paid'")->fetch_assoc()['total'] ?? 0;

// Get recent classes
$recent_classes = $conn->query("
    SELECT c.* FROM classes c
    JOIN student_classes sc ON c.id = sc.class_id
    WHERE sc.student_id = $student_id AND c.status='active'
    LIMIT 5
");

// Get recent fees
$recent_fees = $conn->query("
    SELECT f.*, c.class_name, c.subject 
    FROM fee_records f
    JOIN classes c ON f.class_id = c.id
    WHERE f.student_id = $student_id
    ORDER BY f.created_at DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1>Welcome, <?php echo $student['name']; ?>! 👋</h1>
                
                <div class="stats-grid">
                    <div class="stat-card blue">
                        <div class="stat-icon">📚</div>
                        <div class="stat-info">
                            <h3><?php echo $classes_count; ?></h3>
                            <p>Enrolled Classes</p>
                        </div>
                    </div>
                    
                    <div class="stat-card orange">
                        <div class="stat-icon">⏰</div>
                        <div class="stat-info">
                            <h3><?php echo $pending_fees; ?></h3>
                            <p>Pending Fees</p>
                        </div>
                    </div>
                    
                    <div class="stat-card green">
                        <div class="stat-icon">✅</div>
                        <div class="stat-info">
                            <h3><?php echo formatCurrency($total_paid); ?></h3>
                            <p>Total Paid</p>
                        </div>
                    </div>
                    
                    <div class="stat-card purple">
                        <div class="stat-icon">👤</div>
                        <div class="stat-info">
                            <h3><?php echo $student['student_id']; ?></h3>
                            <p>Student ID</p>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-grid">
                    <div class="card">
                        <div class="card-header">
                            <h2>My Classes</h2>
                            <a href="classes.php" class="btn-small">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Teacher</th>
                                        <th>Schedule</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recent_classes->num_rows > 0): ?>
                                        <?php while ($class = $recent_classes->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $class['class_name']; ?></td>
                                            <td><?php echo $class['subject']; ?></td>
                                            <td><?php echo $class['teacher_name']; ?></td>
                                            <td><?php echo $class['schedule']; ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 2rem;">No classes enrolled</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2>Fee Status</h2>
                            <a href="fees.php" class="btn-small">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recent_fees->num_rows > 0): ?>
                                        <?php while ($fee = $recent_fees->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $fee['class_name']; ?></td>
                                            <td><?php echo formatCurrency($fee['amount']); ?></td>
                                            <td><?php echo $fee['due_date'] ? formatDate($fee['due_date']) : '-'; ?></td>
                                            <td><span class="badge badge-<?php echo $fee['status']; ?>"><?php echo ucfirst($fee['status']); ?></span></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 2rem;">No fee records</td>
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