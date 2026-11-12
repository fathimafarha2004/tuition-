<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// Get statistics
$total_students = $conn->query("SELECT COUNT(*) as count FROM students WHERE status='active'")->fetch_assoc()['count'];
$total_classes = $conn->query("SELECT COUNT(*) as count FROM classes WHERE status='active'")->fetch_assoc()['count'];
$pending_fees = $conn->query("SELECT COUNT(*) as count FROM fee_records WHERE status='pending'")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(amount) as total FROM fee_records WHERE status='paid'")->fetch_assoc()['total'] ?? 0;

// Recent students
$recent_students = $conn->query("SELECT * FROM students ORDER BY created_at DESC LIMIT 5");

// Recent fee records
$recent_fees = $conn->query("
    SELECT f.*, s.name as student_name, c.class_name, c.subject 
    FROM fee_records f
    JOIN students s ON f.student_id = s.id
    JOIN classes c ON f.class_id = c.id
    ORDER BY f.created_at DESC LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1>Dashboard Overview</h1>
                
                <div class="stats-grid">
                    <div class="stat-card blue">
                        <div class="stat-icon">👥</div>
                        <div class="stat-info">
                            <h3><?php echo $total_students; ?></h3>
                            <p>Active Students</p>
                        </div>
                    </div>
                    
                    <div class="stat-card green">
                        <div class="stat-icon">📚</div>
                        <div class="stat-info">
                            <h3><?php echo $total_classes; ?></h3>
                            <p>Active Classes</p>
                        </div>
                    </div>
                    
                    <div class="stat-card orange">
                        <div class="stat-icon">⏰</div>
                        <div class="stat-info">
                            <h3><?php echo $pending_fees; ?></h3>
                            <p>Pending Fees</p>
                        </div>
                    </div>
                    
                    <div class="stat-card purple">
                        <div class="stat-icon">💰</div>
                        <div class="stat-info">
                            <h3><?php echo formatCurrency($total_revenue); ?></h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Students -->
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-header">
                        <h2>Recent Students</h2>
                        <a href="students.php" class="btn-small">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($student = $recent_students->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $student['student_id']; ?></td>
                                    <td><?php echo $student['name']; ?></td>
                                    <td><?php echo $student['email']; ?></td>
                                    <td><span class="badge badge-<?php echo $student['status']; ?>"><?php echo ucfirst($student['status']); ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Recent Fee Records -->
                <div class="card">
                    <div class="card-header">
                        <h2>Recent Fee Records</h2>
                        <a href="fees.php" class="btn-small">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Class</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($fee = $recent_fees->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $fee['student_name']; ?></td>
                                    <td><?php echo $fee['class_name'] . ' - ' . $fee['subject']; ?></td>
                                    <td><?php echo formatCurrency($fee['amount']); ?></td>
                                    <td><span class="badge badge-<?php echo $fee['status']; ?>"><?php echo ucfirst($fee['status']); ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>