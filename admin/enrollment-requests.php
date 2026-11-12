<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    redirect('login.php');
}

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = (int)$_POST['request_id'];
    $action = sanitize($_POST['action']);
    $response = sanitize($_POST['response']);
    
    if ($action == 'approve') {
        // Get request details
        $request = $conn->query("SELECT * FROM enrollment_requests WHERE id=$request_id")->fetch_assoc();
        
        if ($request) {
            // Add to student_classes
            $student_id = $request['student_id'];
            $class_id = $request['class_id'];
            $conn->query("INSERT INTO student_classes (student_id, class_id) VALUES ($student_id, $class_id)");
            
            // Update request status
            $conn->query("UPDATE enrollment_requests SET status='approved', admin_response='$response', responded_at=NOW() WHERE id=$request_id");
            $_SESSION['message'] = 'Enrollment request approved and student enrolled!';
        }
    } elseif ($action == 'reject') {
        $conn->query("UPDATE enrollment_requests SET status='rejected', admin_response='$response', responded_at=NOW() WHERE id=$request_id");
        $_SESSION['message'] = 'Enrollment request rejected.';
    }
    
    redirect('enrollment-requests.php');
}

// Get pending requests
$pending = $conn->query("
    SELECT r.*, s.name as student_name, s.student_id as student_sid, s.email,
           c.class_name, c.subject, c.fee_amount
    FROM enrollment_requests r
    JOIN students s ON r.student_id = s.id
    JOIN classes c ON r.class_id = c.id
    WHERE r.status = 'pending'
    ORDER BY r.requested_at ASC
");

// Get processed requests (recent 20)
$processed = $conn->query("
    SELECT r.*, s.name as student_name, s.student_id as student_sid,
           c.class_name, c.subject
    FROM enrollment_requests r
    JOIN students s ON r.student_id = s.id
    JOIN classes c ON r.class_id = c.id
    WHERE r.status IN ('approved', 'rejected')
    ORDER BY r.responded_at DESC
    LIMIT 20
");

$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
unset($_SESSION['message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Requests - Simple Tuition</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/header.php'; ?>
            
            <div class="content-area">
                <h1>Enrollment Requests</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <!-- Pending Requests -->
                <div class="card" style="margin-bottom: 2rem;">
                    <div class="card-header">
                        <h2>Pending Requests (<?php echo $pending->num_rows; ?>)</h2>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Class Requested</th>
                                    <th>Subject</th>
                                    <th>Fee</th>
                                    <th>Message</th>
                                    <th>Requested Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($pending->num_rows > 0): ?>
                                    <?php while ($req = $pending->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $req['student_name']; ?></strong><br>
                                            <small style="color: var(--gray-600);"><?php echo $req['student_sid']; ?></small>
                                        </td>
                                        <td><?php echo $req['class_name']; ?></td>
                                        <td><?php echo $req['subject']; ?></td>
                                        <td><?php echo formatCurrency($req['fee_amount']); ?></td>
                                        <td><?php echo $req['request_message'] ?: '-'; ?></td>
                                        <td><?php echo formatDate($req['requested_at']); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button onclick="showResponseModal(<?php echo $req['id']; ?>, 'approve', '<?php echo addslashes($req['student_name']); ?>', '<?php echo addslashes($req['class_name']); ?>')" 
                                                        class="btn-icon" style="background: var(--success-light);" title="Approve">
                                                    ✅
                                                </button>
                                                <button onclick="showResponseModal(<?php echo $req['id']; ?>, 'reject', '<?php echo addslashes($req['student_name']); ?>', '<?php echo addslashes($req['class_name']); ?>')" 
                                                        class="btn-icon delete" title="Reject">
                                                    ❌
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 2rem;">No pending requests</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Processed Requests -->
                <div class="card">
                    <div class="card-header">
                        <h2>Recent Processed Requests</h2>
                    </div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Class</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Response</th>
                                    <th>Processed Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($processed->num_rows > 0): ?>
                                    <?php while ($req = $processed->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo $req['student_name']; ?></strong><br>
                                            <small style="color: var(--gray-600);"><?php echo $req['student_sid']; ?></small>
                                        </td>
                                        <td><?php echo $req['class_name']; ?></td>
                                        <td><?php echo $req['subject']; ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $req['status']; ?>">
                                                <?php echo ucfirst($req['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $req['admin_response'] ?: '-'; ?></td>
                                        <td><?php echo formatDate($req['responded_at']); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2rem;">No processed requests yet</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Response Modal -->
    <div id="responseModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%;">
            <h3 style="margin-bottom: 1rem;" id="modal_title"></h3>
            <form method="POST" action="">
                <input type="hidden" name="request_id" id="modal_request_id">
                <input type="hidden" name="action" id="modal_action">
                
                <div class="form-group">
                    <label>Student:</label>
                    <p id="modal_student" style="font-weight: 600;"></p>
                </div>
                
                <div class="form-group">
                    <label>Class:</label>
                    <p id="modal_class" style="font-weight: 600; color: var(--primary);"></p>
                </div>
                
                <div class="form-group">
                    <label for="response">Response Message (Optional)</label>
                    <textarea name="response" id="response" rows="4" placeholder="Add a message to the student..."></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn" id="modal_submit"></button>
                    <button type="button" onclick="closeResponseModal()" class="btn btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showResponseModal(requestId, action, studentName, className) {
            document.getElementById('modal_request_id').value = requestId;
            document.getElementById('modal_action').value = action;
            document.getElementById('modal_student').textContent = studentName;
            document.getElementById('modal_class').textContent = className;
            
            if (action === 'approve') {
                document.getElementById('modal_title').textContent = 'Approve Enrollment Request';
                document.getElementById('modal_submit').textContent = 'Approve & Enroll';
                document.getElementById('modal_submit').className = 'btn btn-success';
            } else {
                document.getElementById('modal_title').textContent = 'Reject Enrollment Request';
                document.getElementById('modal_submit').textContent = 'Reject Request';
                document.getElementById('modal_submit').className = 'btn btn-danger';
            }
            
            document.getElementById('responseModal').style.display = 'flex';
        }
        
        function closeResponseModal() {
            document.getElementById('responseModal').style.display = 'none';
            document.getElementById('response').value = '';
        }
        
        // Close modal when clicking outside
        document.getElementById('responseModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeResponseModal();
            }
        });
    </script>
</body>
</html>
