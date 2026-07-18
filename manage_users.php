<?php
require_once "config.php";
require_once "includes/auth.php";
requireAdmin(); // only admins can manage users

$pageTitle = "Manage Users";
$error = "";
//hello
$success = "";

// Handle "create new user" form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? 'user';

    if ($username === '' || $password === '') {
        $error = "Username and password are required.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (!in_array($role, ['admin', 'user'])) {
        $error = "Invalid role selected.";
    } else {
        // Check the username isn't already taken
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        if ($check->fetch()) {
            $error = "That username is already taken.";
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashed, $role]);
            $success = "Account for \"$username\" created successfully.";
        }
    }
}

// Handle delete user (can't delete yourself)
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    if ($deleteId === (int)$_SESSION['user_id']) {
        $error = "You cannot delete your own account while logged in.";
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$deleteId]);
        $success = "User deleted.";
    }
}

// Get all users for the table
$users = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

include "includes/header.php";
?>

<div class="page-head">
    <div>
        <h1>Manage Users</h1>
        <p class="page-subtitle">Create new accounts and manage existing ones. Only admins can access this page.</p>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<div class="two-col">
    <div class="form-card">
        <h2 class="card-title">Create New Account</h2>
        <form method="POST" action="manage_users.php">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="6">
                <span class="field-hint">Minimum 6 characters</span>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="user">User (view & add only)</option>
                    <option value="admin">Admin (full access)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Create Account</button>
        </form>
    </div>

    <div class="table-wrapper">
        <h2 class="card-title">Existing Users</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th class="actions-col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td data-label="Username"><?php echo htmlspecialchars($u['username']); ?></td>
                    <td data-label="Role">
                        <span class="badge <?php echo $u['role'] === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                            <?php echo ucfirst($u['role']); ?>
                        </span>
                    </td>
                    <td data-label="Joined"><?php echo date("M d, Y", strtotime($u['created_at'])); ?></td>
                    <td data-label="Actions" class="actions-col">
                        <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                            <a href="manage_users.php?delete=<?php echo $u['id']; ?>"
                               class="btn btn-small btn-danger"
                               onclick="return confirm('Delete this user account?');">Delete</a>
                        <?php else: ?>
                            <span class="field-hint">This is you</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "includes/footer.php"; ?>
