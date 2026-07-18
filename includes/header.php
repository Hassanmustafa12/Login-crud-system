<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? $pageTitle . " - RecordHub" : "RecordHub"; ?></title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
<nav class="navbar">
    <div class="nav-brand">📋 RecordHub</div>
    <div class="nav-links">
        <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a>
        <a href="add_record.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'add_record.php' ? 'active' : ''; ?>">Add Record</a>
        <?php if (isAdmin()): ?>
            <a href="manage_users.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'manage_users.php' ? 'active' : ''; ?>">Manage Users</a>
        <?php endif; ?>
    </div>
    <div class="nav-user">
        <span class="badge <?php echo isAdmin() ? 'badge-admin' : 'badge-user'; ?>">
            <?php echo isAdmin() ? 'Admin' : 'User'; ?>
        </span>
        <span class="nav-username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
</nav>
<?php endif; ?>

<main class="container">
