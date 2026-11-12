<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM classes WHERE id = $id");
    $_SESSION['message'] = 'Class deleted successfully!';
    redirect('classes.php');
}

// Handle status toggle
if (isset($_GET['toggle_status'])) {
    $id = (int)$_GET['toggle_status'];
    $conn->query("UPDATE classes SET status = IF(status='active', 'inactive', 'active') WHERE id = $id");
    redirect('classes.php');
}

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$where = $search ? "WHERE class_name LIKE '%$search%' OR subject LIKE '%$search%' OR teacher_name LIKE '%$search%'" : '';

$classes = $conn->query("SELECT * FROM classes $where ORDER BY created_at DESC");

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes Management - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1>Classes Management</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <div class="actions">
                    <div class="search-box">
                        <form method="GET" action="">
                            <input type="text" name="search" placeholder="Search classes..." value="<?php echo $search; ?>">
                        </form>
                    </div>
                    <a href="class-form.php" class="btn">+ Add New Class</a>
                </div>
                
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
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($classes->num_rows > 0): ?>
                                    <?php while ($class = $classes->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $class['class_name']; ?></td>
                                        <td><?php echo $class['subject']; ?></td>
                                        <td><?php echo $class['teacher_name']; ?></td>
                                        <td><?php echo $class['schedule']; ?></td>
                                        <td><?php echo $class['room_number']; ?></td>
                                        <td><?php echo formatCurrency($class['fee_amount']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $class['status']; ?>">
                                                <?php echo ucfirst($class['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="class-form.php?id=<?php echo $class['id']; ?>" class="btn-icon edit" title="Edit">✏️</a>
                                                <a href="?toggle_status=<?php echo $class['id']; ?>" class="btn-icon" title="Toggle Status" onclick="return confirm('Toggle class status?')">🔄</a>
                                                <a href="?delete=<?php echo $class['id']; ?>" class="btn-icon delete" title="Delete" onclick="return confirm('Delete this class?')">🗑️</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 2rem;">No classes found</td>
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