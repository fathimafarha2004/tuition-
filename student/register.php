<?php
require_once '../config.php';

if (isStudentLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = sanitize($_POST['address']);
    $date_of_birth = sanitize($_POST['date_of_birth']);
    
    // Validation
    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if email already exists
        $check = $conn->query("SELECT id FROM students WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $error = 'Email already registered. Please login instead.';
        } else {
            // Generate unique student ID
            $year = date('Y');
            $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $student_id = 'ST' . $year . $random;
            
            // Check if student_id exists (unlikely but possible)
            while ($conn->query("SELECT id FROM students WHERE student_id = '$student_id'")->num_rows > 0) {
                $random = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                $student_id = 'ST' . $year . $random;
            }
            
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $enrollment_date = date('Y-m-d');
            
            $query = "INSERT INTO students (student_id, name, email, phone, password, address, date_of_birth, enrollment_date, status) 
                      VALUES ('$student_id', '$name', '$email', '$phone', '$hashed_password', '$address', '$date_of_birth', '$enrollment_date', 'active')";
            
            if ($conn->query($query)) {
                $success = "Registration successful! Your Student ID is: <strong>$student_id</strong><br>Please save this ID for future login.";
                
                // Auto login
                $new_student = $conn->query("SELECT * FROM students WHERE student_id = '$student_id'")->fetch_assoc();
                $_SESSION['student_id'] = $new_student['id'];
                $_SESSION['student_name'] = $new_student['name'];
                $_SESSION['student_sid'] = $new_student['student_id'];
                
                // Redirect after 3 seconds
                header("refresh:3;url=dashboard.php");
            } else {
                $error = 'Registration failed. Please try again. Error: ' . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - Simple Tuition</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .register-container {
            background: white;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 600px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .register-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 40px;
        }
        
        h1 {
            color: #333;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        
        .subtitle {
            color: #666;
            font-size: 0.9rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        label .required {
            color: red;
        }
        
        input, textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
            font-family: inherit;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .error {
            background: #fee;
            color: #c33;
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .links {
            text-align: center;
            margin-top: 1.5rem;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .register-container {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div class="register-icon">🎓</div>
            <h1>Student Registration</h1>
            <p class="subtitle">Create your account to get started</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?><br>Redirecting to dashboard...</div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" placeholder="0771234567">
                </div>
                
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth">
                </div>
                
                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" placeholder="Your full address"></textarea>
            </div>
            
            <button type="submit" class="btn">Register Now</button>
        </form>
        <?php endif; ?>
        
        <div class="links">
            <a href="login.php">Already have an account? Login</a>
            <a href="../index.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>