<?php
require_once "config.php";
require_once "includes/auth.php";
requireLogin(); // both roles can add records

$pageTitle = "Add Record";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '') {
        $error = "Title is required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO records (title, description, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $_SESSION['user_id']]);

        header("Location: dashboard.php?success=" . urlencode("Record added successfully."));
        exit;
    }
}

include "includes/header.php";
?>

<div class="page-head">
    <div>
        <h1>Add Record</h1>
        <p class="page-subtitle">Fill in the details below to create a new record.</p>
    </div>
</div>

<div class="form-card">
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="add_record.php">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required
                   value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
        </div>
        <div class="form-actions">
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Record</button>
        </div>
    </form>
</div>

<?php include "includes/footer.php"; ?>
