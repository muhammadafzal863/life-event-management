<?php
require_once 'includes/auth.php';
if (isLoggedIn()) {
    redirect('/life-event-management/dashboard.php');
} else {
    redirect('/life-event-management/auth/login.php');
}
?>