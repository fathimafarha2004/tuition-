<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <span class="logo-icon">🎓</span>
            <span>Simple Tuition</span>
        </div>
    </div>
    
    <nav class="sidebar-menu">
        <a href="dashboard.php" class="menu-item <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
            <span class="menu-icon">📊</span>
            <span>Dashboard</span>
        </a>
        
        <a href="students.php" class="menu-item <?php echo $current_page == 'students.php' || $current_page == 'student-form.php' ? 'active' : ''; ?>">
            <span class="menu-icon">👥</span>
            <span>Students</span>
        </a>
        
        <a href="classes.php" class="menu-item <?php echo $current_page == 'classes.php' || $current_page == 'class-form.php' ? 'active' : ''; ?>">
            <span class="menu-icon">📚</span>
            <span>Classes</span>
        </a>
        
        <a href="fees.php" class="menu-item <?php echo $current_page == 'fees.php' || $current_page == 'fee-form.php' ? 'active' : ''; ?>">
            <span class="menu-icon">💰</span>
            <span>Fee Records</span>
        </a>
        
        <a href="enrollment-requests.php" class="menu-item <?php echo $current_page == 'enrollment-requests.php' ? 'active' : ''; ?>">
            <span class="menu-icon">📝</span>
            <span>Enrollment Requests</span>
        </a>
        
        <a href="attendance.php" class="menu-item <?php echo $current_page == 'attendance.php' ? 'active' : ''; ?>">
            <span class="menu-icon">✅</span>
            <span>Attendance</span>
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">👨‍💼</div>
            <div class="user-details">
                <h4><?php echo $_SESSION['admin_username']; ?></h4>
                <p>Administrator</p>
            </div>
        </div>
        <a href="logout.php" class="menu-item">
            <span class="menu-icon">🚪</span>
            <span>Logout</span>
        </a>
    </div>
</div>