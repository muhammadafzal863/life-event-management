<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);
$event = $stmt->fetch();

if (!$event) {
    redirect('index.php');
}

$cats = $pdo->query("SELECT * FROM event_categories")->fetchAll();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category_id = $_POST['category_id'];
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = trim($_POST['location']);
    $visibility = $_POST['visibility'];

    if (empty($title) || empty($event_date)) {
        $error = 'Title and date are required.';
    } else {
        $image = $event['image'];
        if (!empty($_FILES['image']['name'])) {
            $target_dir = "../assets/images/";
            $image = basename($_FILES['image']['name']);
            $target_file = $target_dir . $image;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowed) && $_FILES['image']['size'] < 5000000) {
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                    $error = 'Failed to upload image.';
                }
            } else {
                $error = 'Invalid image format or size (max 5MB).';
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare("UPDATE events SET category_id=?, title=?, description=?, event_date=?, event_time=?, location=?, image=?, visibility=? WHERE id=? AND user_id=?");
            if ($stmt->execute([$category_id, $title, $description, $event_date, $event_time, $location, $image, $visibility, $id, $user_id])) {
                $success = 'Event updated successfully.';
                $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
                $stmt->execute([$id]);
                $event = $stmt->fetch();
            } else {
                $error = 'Failed to update event.';
            }
        }
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h2>Edit Event</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Title *</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($event['title']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Category</label>
                <select name="category_id" class="form-select">
                    <option value="">Select Category</option>
                    <?php foreach ($cats as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $event['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($event['description']) ?></textarea>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label>Event Date *</label>
                <input type="date" name="event_date" class="form-control" value="<?= htmlspecialchars($event['event_date']) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label>Event Time</label>
                <input type="time" name="event_time" class="form-control" value="<?= htmlspecialchars($event['event_time']) ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label>Location</label>
                <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($event['location']) ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
                <?php if ($event['image']): ?>
                    <img src="../assets/images/<?= htmlspecialchars($event['image']) ?>" width="100" class="mt-2">
                <?php endif; ?>
            </div>
            <div class="col-md-6 mb-3">
                <label>Visibility</label>
                <select name="visibility" class="form-select">
                    <option value="private" <?= $event['visibility'] == 'private' ? 'selected' : '' ?>>Private</option>
                    <option value="public" <?= $event['visibility'] == 'public' ? 'selected' : '' ?>>Public</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Event</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>