<?php
require_once "config.php";
require_once "includes/auth.php";
requireLogin(); // both admin and normal users can see this page

$pageTitle = "Dashboard";

// Fetch all records, along with the username of whoever created them
$stmt = $pdo->query("
    SELECT records.*, users.username AS creator
    FROM records
    LEFT JOIN users ON records.created_by = users.id
    ORDER BY records.created_at DESC
");
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "includes/header.php";
?>

<div class="page-head">
    <div>
        <h1>Records</h1>
        <p class="page-subtitle">
            <?php echo isAdmin()
                ? "As an admin you can add, edit, and delete records."
                : "You can view all records and add new ones."; ?>
        </p>
    </div>
    <a href="add_record.php" class="btn btn-primary">+ Add Record</a>
</div>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<?php if (count($records) === 0): ?>
    <div class="empty-state">
        <p>No records yet. Click "Add Record" to create the first one.</p>
    </div>
<?php else: ?>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Added By</th>
                    <th>Created</th>
                    <?php if (isAdmin()): ?><th class="actions-col">Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                <tr>
                    <td data-label="Title"><?php echo htmlspecialchars($record['title']); ?></td>
                    <td data-label="Description"><?php echo nl2br(htmlspecialchars($record['description'])); ?></td>
                    <td data-label="Added By"><?php echo htmlspecialchars($record['creator'] ?? 'Unknown'); ?></td>
                    <td data-label="Created"><?php echo date("M d, Y", strtotime($record['created_at'])); ?></td>
                    <?php if (isAdmin()): ?>
                    <td data-label="Actions" class="actions-col">
                        <a href="edit_record.php?id=<?php echo $record['id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                        <a href="delete_record.php?id=<?php echo $record['id']; ?>"
                           class="btn btn-small btn-danger"
                           onclick="return confirm('Delete this record? This cannot be undone.');">Delete</a>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php include "includes/footer.php"; ?>
