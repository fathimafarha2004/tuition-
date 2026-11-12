
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
        
        <a href="profile.php" class="menu-item <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>">
            <span class="menu-icon">👤</span>
            <span>My Profile</span>
        </a>
        
        <a href="classes.php" class="menu-item <?php echo $current_page == 'classes.php' ? 'active' : ''; ?>">
            <span class="menu-icon">📚</span>
            <span>My Classes</span>
        </a>
        
        <a href="fees.php" class="menu-item <?php echo $current_page == 'fees.php' ? 'active' : ''; ?>">
            <span class="menu-icon">💰</span>
            <span>Fee Status</span>
        </a>
        
        <a href="request-enrollment.php" class="menu-item <?php echo $current_page == 'request-enrollment.php' ? 'active' : ''; ?>">
            <span class="menu-icon">📝</span>
            <span>Request Enrollment</span>
        </a>
        
        <a href="attendance.php" class="menu-item <?php echo $current_page == 'attendance.php' ? 'active' : ''; ?>">
            <span class="menu-icon">✅</span>
            <span>Attendance</span>
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">🎓</div>
            <div class="user-details">
                <h4><?php echo $_SESSION['student_name']; ?></h4>
                <p><?php echo $_SESSION['student_sid']; ?></p>
            </div>
        </div>
        <a href="logout.php" class="menu-item">
            <span class="menu-icon">🚪</span>
            <span>Logout</span>
        </a>
    </div>
</div>