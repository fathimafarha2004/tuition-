<?php
// admin/logout.php
require_once '../config.php';
session_destroy();
redirect('../index.php');

// ============================================
// student/logout.php (same content)
// ============================================
// <?php
// require_once '../config.php';
// session_destroy();
// redirect('../index.php');
?>