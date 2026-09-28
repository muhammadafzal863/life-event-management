<?php
require_once '../config/database.php';
require_once '../includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $user_id]);

redirect('index.php');
?>