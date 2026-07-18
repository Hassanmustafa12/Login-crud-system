<?php
require_once "config.php";
require_once "includes/auth.php";
requireAdmin(); // only admins may edit

$pageTitle = "Edit Record";
$error = "";

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    header("Location: dashboard.php?error=" . urlencode("No record specified."));
    exit;
}

// Load the existing record
$stmt = $pdo->prepare("SELECT * FROM records WHERE id = ?");
$stmt->execute([$id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    header("Location: dashboard.php?error=" . urlencode("Record not found."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '') {
        $error = "Title is required.";
    } else {
        $stmt = $pdo->prepare("UPDATE records SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $description, $id]);

        header("Location: dashboard.php?success=" . urlencode("Record updated successfully."));
        exit;
    }
    // keep the attempted values on screen if there was an error
    $record['title'] = $title;
    $record['description'] = $description;
}

include "includes/header.php";
?>

<div class="page-head">
    <div>
        <h1>Edit Record</h1>
        <p class="page-subtitle">Update the details below and save your changes.</p>
    </div>
</div>

<div class="form-card">
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="edit_record.php">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($record['id']); ?>">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required
                   value="<?php echo htmlspecialchars($record['title']); ?>">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($record['description']); ?></textarea>
        </div>
        <div class="form-actions">
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Record</button>
        </div>
    </form>
</div>

<?php include "includes/footer.php"; ?>
