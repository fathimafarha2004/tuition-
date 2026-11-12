<?php
require_once '../config.php';

if (!isStudentLoggedIn()) {
    redirect('login.php');
}

$student_id = $_SESSION['student_id'];

$attendance = $conn->query("
    SELECT a.*, c.class_name, c.subject 
    FROM attendance a
    JOIN classes c ON a.class_id = c.id
    WHERE a.student_id = $student_id
    ORDER BY a.date DESC
");

// Calculate statistics
$total_days = 0;
$present_count = 0;
$absent_count = 0;
$late_count = 0;

$stats = $conn->query("SELECT status, COUNT(*) as count FROM attendance WHERE student_id = $student_id GROUP BY status");
while ($stat = $stats->fetch_assoc()) {
    $total_days += $stat['count'];
    if ($stat['status'] == 'present') $present_count = $stat['count'];
    if ($stat['status'] == 'absent') $absent_count = $stat['count'];
    if ($stat['status'] == 'late') $late_count = $stat['count'];
}

$attendance_rate = $total_days > 0 ? round(($present_count / $total_days) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1>My Attendance</h1>
                
                <div class="stats-grid">
                    <div class="stat-card blue">
                        <div class="stat-icon">📊</div>
                        <div class="stat-info">
                            <h3><?php echo $total_days; ?></h3>
                            <p>Total Days</p>
                        </div>
                    </div>
                    
                    <div class="stat-card green">
                        <div class="stat-icon">✅</div>
                        <div class="stat-info">
                            <h3><?php echo $present_count; ?></h3>
                            <p>Present</p>
                        </div>
                    </div>
                    
                    <div class="stat-card orange">
                        <div class="stat-icon">⏰</div>
                        <div class="stat-info">
                            <h3><?php echo $late_count; ?></h3>
                            <p>Late</p>
                        </div>
                    </div>
                    
                    <div class="stat-card purple">
                        <div class="stat-icon">❌</div>
                        <div class="stat-info">
                            <h3><?php echo $absent_count; ?></h3>
                            <p>Absent</p>
                        </div>
                    </div>
                </div>
                
                <div style="background: white; padding: 2rem; border-radius: 12px; margin-bottom: 2rem; text-align: center;">
                    <h2 style="margin-bottom: 1rem; color: #333;">Attendance Rate</h2>
                    <div style="font-size: 3rem; font-weight: bold; color: <?php echo $attendance_rate >= 75 ? '#38ef7d' : '#f5576c'; ?>;">
                        <?php echo $attendance_rate; ?>%
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2>Attendance History</h2>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Class - Subject</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($attendance->num_rows > 0): ?>
                                    <?php while ($record = $attendance->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo formatDate($record['date']); ?></strong></td>
                                        <td><?php echo $record['class_name'] . ' - ' . $record['subject']; ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $record['status']; ?>">
                                                <?php 
                                                    $icons = ['present' => '✅', 'absent' => '❌', 'late' => '⏰'];
                                                    echo $icons[$record['status']] . ' ' . ucfirst($record['status']); 
                                                ?>
                                            </span>
                                        </td>
                                        <td><?php echo $record['notes'] ?: '-'; ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 2rem;">
                                            No attendance records found.
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