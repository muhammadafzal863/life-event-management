<?php
// Sahi path: profile folder se ek step peeche (root) jana hai
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($name)) {
        $error = 'Name is required.';
    } else {
        $image = $user['profile_image'];
        if (!empty($_FILES['profile_image']['name'])) {
            $target_dir = "../assets/images/";
            $image = basename($_FILES['profile_image']['name']);
            $target_file = $target_dir . $image;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($imageFileType, $allowed) && $_FILES['profile_image']['size'] < 5000000) {
                if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                    $error = 'Failed to upload image.';
                }
            } else {
                $error = 'Invalid image format or size (max 5MB).';
            }
        }

        if (!$error) {
            if (!empty($password)) {
                if ($password !== $confirm) {
                    $error = 'Passwords do not match.';
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET name=?, phone=?, profile_image=?, password=? WHERE id=?");
                    $stmt->execute([$name, $phone, $image, $hashed, $user_id]);
                }
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name=?, phone=?, profile_image=? WHERE id=?");
                $stmt->execute([$name, $phone, $image, $user_id]);
            }
            if (!$error) {
                $success = 'Profile updated successfully.';
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
                $_SESSION['user_name'] = $user['name'];
            }
        }
    }
}

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="container mt-4">
    <h2>My Profile</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Name *</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Email (cannot be changed)</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label>Profile Image</label>
                <input type="file" name="profile_image" class="form-control">
                <?php if ($user['profile_image']): ?>
                    <img src="../assets/images/<?= htmlspecialchars($user['profile_image']) ?>" width="100" class="mt-2">
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>New Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>