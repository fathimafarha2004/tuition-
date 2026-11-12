<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM students WHERE id = $id");
    $_SESSION['message'] = 'Student deleted successfully!';
    redirect('students.php');
}

// Handle status toggle
if (isset($_GET['toggle_status'])) {
    $id = (int)$_GET['toggle_status'];
    $conn->query("UPDATE students SET status = IF(status='active', 'inactive', 'active') WHERE id = $id");
    redirect('students.php');
}

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$where = $search ? "WHERE name LIKE '%$search%' OR student_id LIKE '%$search%' OR email LIKE '%$search%'" : '';

$students = $conn->query("SELECT * FROM students $where ORDER BY created_at DESC");

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Management - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1>Students Management</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <div class="actions">
                    <div class="search-box">
                        <form method="GET" action="">
                            <input type="text" name="search" placeholder="Search students..." value="<?php echo $search; ?>">
                        </form>
                    </div>
                    <a href="student-form.php" class="btn">+ Add New Student</a>
                </div>
                
                <div class="card">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($students->num_rows > 0): ?>
                                    <?php while ($student = $students->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $student['student_id']; ?></td>
                                        <td><?php echo $student['name']; ?></td>
                                        <td><?php echo $student['email']; ?></td>
                                        <td><?php echo $student['phone']; ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $student['status']; ?>">
                                                <?php echo ucfirst($student['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($student['created_at']); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="enrollments.php?student_id=<?php echo $student['id']; ?>" class="btn-icon" style="background: var(--success-light);" title="Manage Enrollments">📚</a>
                                                <a href="student-form.php?id=<?php echo $student['id']; ?>" class="btn-icon edit" title="Edit">✏️</a>
                                                <a href="?toggle_status=<?php echo $student['id']; ?>" class="btn-icon" title="Toggle Status" onclick="return confirm('Toggle student status?')">🔄</a>
                                                <a href="?delete=<?php echo $student['id']; ?>" class="btn-icon delete" title="Delete" onclick="return confirm('Delete this student?')">🗑️</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 2rem;">No students found</td>
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
