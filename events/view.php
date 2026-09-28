<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT e.*, c.name as category_name FROM events e 
                       LEFT JOIN event_categories c ON e.category_id = c.id 
                       WHERE e.id = ? AND e.user_id = ?");
$stmt->execute([$id, $user_id]);
$event = $stmt->fetch();

if (!$event) {
    redirect('index.php');
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h2><?= htmlspecialchars($event['title']) ?></h2>
    <div class="card">
        <div class="card-body">
            <?php if ($event['image']): ?>
                <img src="../assets/images/<?= htmlspecialchars($event['image']) ?>" class="img-fluid mb-3" style="max-height: 300px;">
            <?php endif; ?>
            <p><strong>Category:</strong> <?= htmlspecialchars($event['category_name'] ?? 'Uncategorized') ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?></p>
            <p><strong>Time:</strong> <?= htmlspecialchars($event['event_time'] ?? 'N/A') ?></p>
            <p><strong>Location:</strong> <?= htmlspecialchars($event['location'] ?? 'N/A') ?></p>
            <p><strong>Visibility:</strong> <?= ucfirst($event['visibility']) ?></p>
            <p><strong>Description:</strong></p>
            <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
        </div>
    </div>
    <div class="mt-3">
        <a href="edit.php?id=<?= $event['id'] ?>" class="btn btn-warning">Edit</a>
        <a href="delete.php?id=<?= $event['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this event?')">Delete</a>
        <a href="index.php" class="btn btn-secondary">Back to Events</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>