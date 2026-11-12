<?php
require_once '../config.php';

if (!isStudentLoggedIn()) {
    redirect('login.php');
}

$student_id = $_SESSION['student_id'];

$classes = $conn->query("
    SELECT c.*, sc.enrolled_date 
    FROM classes c
    JOIN student_classes sc ON c.id = sc.class_id
    WHERE sc.student_id = $student_id
    ORDER BY c.class_name
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Classes - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1>My Classes</h1>
                
                <div class="card">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Class Name</th>
                                    <th>Subject</th>
                                    <th>Teacher</th>
                                    <th>Schedule</th>
                                    <th>Room</th>
                                    <th>Fee Amount</th>
                                    <th>Enrolled Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($classes->num_rows > 0): ?>
                                    <?php while ($class = $classes->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong><?php echo $class['class_name']; ?></strong></td>
                                        <td><?php echo $class['subject']; ?></td>
                                        <td><?php echo $class['teacher_name']; ?></td>
                                        <td><?php echo $class['schedule']; ?></td>
                                        <td><?php echo $class['room_number']; ?></td>
                                        <td><?php echo formatCurrency($class['fee_amount']); ?></td>
                                        <td><?php echo formatDate($class['enrolled_date']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $class['status']; ?>">
                                                <?php echo ucfirst($class['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 2rem;">
                                            You are not enrolled in any classes yet.
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