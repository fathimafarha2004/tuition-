<div class="top-header">
    <div>
        <h2 style="color: #333; font-size: 1.2rem;">Welcome, <?php echo $_SESSION['admin_username']; ?>!</h2>
        <p style="color: #666; font-size: 0.9rem; margin-top: 0.2rem;">
            <?php echo date('l, F j, Y'); ?>
        </p>
    </div>
    <div style="display: flex; gap: 1rem; align-items: center;">
        <a href="dashboard.php" style="text-decoration: none; color: #667eea; font-weight: 600;">
            🏠 Dashboard
        </a>
    </div>
</div>