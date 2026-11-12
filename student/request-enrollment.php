<?php
require_once '../config.php';

if (!isStudentLoggedIn()) {
    redirect('login.php');
}

$student_id = $_SESSION['student_id'];

// Handle enrollment request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_enrollment'])) {
    $class_id = (int)$_POST['class_id'];
    $message = sanitize($_POST['message']);
    
    // Check if already enrolled
    $check_enrolled = $conn->query("SELECT id FROM student_classes WHERE student_id=$student_id AND class_id=$class_id");
    
    // Check if already requested
    $check_request = $conn->query("SELECT id FROM enrollment_requests WHERE student_id=$student_id AND class_id=$class_id AND status='pending'");
    
    if ($check_enrolled->num_rows > 0) {
        $error = 'You are already enrolled in this class!';
    } elseif ($check_request->num_rows > 0) {
        $error = 'You already have a pending request for this class!';
    } else {
        $conn->query("INSERT INTO enrollment_requests (student_id, class_id, request_message) VALUES ($student_id, $class_id, '$message')");
        $_SESSION['message'] = 'Enrollment request sent successfully!';
        redirect('request-enrollment.php');
    }
}

// Get student's enrolled classes
$enrolled_ids = [];
$enrolled_result = $conn->query("SELECT class_id FROM student_classes WHERE student_id=$student_id");
while ($row = $enrolled_result->fetch_assoc()) {
    $enrolled_ids[] = $row['class_id'];
}

// Get pending requests
$pending_ids = [];
$pending_result = $conn->query("SELECT class_id FROM enrollment_requests WHERE student_id=$student_id AND status='pending'");
while ($row = $pending_result->fetch_assoc()) {
    $pending_ids[] = $row['class_id'];
}

// Build exclusion list
$exclude_list = array_merge($enrolled_ids, $pending_ids);
$exclude_clause = count($exclude_list) > 0 ? "AND c.id NOT IN (" . implode(',', $exclude_list) . ")" : '';

// Get available classes
$available = $conn->query("
    SELECT c.* 
    FROM classes c
    WHERE c.status = 'active' $exclude_clause
    ORDER BY c.class_name
");

// Get my requests
$my_requests = $conn->query("
    SELECT r.*, c.class_name, c.subject, c.schedule
    FROM enrollment_requests r
    JOIN classes c ON r.class_id = c.id
    WHERE r.student_id = $student_id
    ORDER BY r.requested_at DESC
");

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$error = $error ?? '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Enrollment - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1>Request Class Enrollment</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <!-- Available Classes -->
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-header">
                        <h2>Available Classes (<?php echo $available->num_rows; ?>)</h2>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Subject</th>
                                    <th>Teacher</th>
                                    <th>Schedule</th>
                                    <th>Fee</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($available->num_rows > 0): ?>
                                    <?php while ($class = $available->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $class['class_name']; ?></td>
                                        <td><?php echo $class['subject']; ?></td>
                                        <td><?php echo $class['teacher_name'] ?: '-'; ?></td>
                                        <td><?php echo $class['schedule'] ?: '-'; ?></td>
                                        <td><?php echo formatCurrency($class['fee_amount']); ?></td>
                                        <td>
                                            <button onclick="showRequestModal(<?php echo $class['id']; ?>, '<?php echo addslashes($class['class_name']); ?>')" 
                                                    class="btn-small">Request Enrollment</button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2rem;">
                                            No available classes to request
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- My Requests -->
                <div class="card">
                    <div class="card-header">
                        <h2>My Requests (<?php echo $my_requests->num_rows; ?>)</h2>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Requested</th>
                                    <th>Response</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($my_requests->num_rows > 0): ?>
                                    <?php while ($req = $my_requests->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $req['class_name']; ?></td>
                                        <td><?php echo $req['subject']; ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $req['status']; ?>">
                                                <?php echo ucfirst($req['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formatDate($req['requested_at']); ?></td>
                                        <td><?php echo $req['admin_response'] ?: '-'; ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 2rem;">
                                            No enrollment requests yet
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
    
    <!-- Request Modal -->
    <div id="requestModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%;">
            <h3 style="margin-bottom: 1rem;">Request Enrollment</h3>
            <form method="POST" action="">
                <input type="hidden" name="request_enrollment" value="1">
                <input type="hidden" name="class_id" id="modal_class_id">
                
                <div class="form-group">
                    <label>Class:</label>
                    <p id="modal_class_name" style="font-weight: 600; color: var(--primary);"></p>
                </div>
                
                <div class="form-group">
                    <label for="message">Why do you want to join this class? (Optional)</label>
                    <textarea name="message" id="message" rows="4" placeholder="Tell the admin why you're interested in this class..."></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn">Send Request</button>
                    <button type="button" onclick="closeRequestModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showRequestModal(classId, className) {
            document.getElementById('modal_class_id').value = classId;
            document.getElementById('modal_class_name').textContent = className;
            document.getElementById('requestModal').style.display = 'flex';
        }
        
        function closeRequestModal() {
            document.getElementById('requestModal').style.display = 'none';
            document.getElementById('message').value = '';
        }
        
        // Close modal when clicking outside
        document.getElementById('requestModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRequestModal();
            }
        });
    </script>
</body>
</html>
