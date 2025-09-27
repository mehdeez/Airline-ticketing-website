<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'SkyVoyager' ?></title>
    <link rel="stylesheet" href="/skyvoyager-airline/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="/skyvoyager-airline/flights/search.php" class="logo">
                <i class="fas fa-plane"></i>
                <span>Sky</span>Voyager
            </a>
            <nav>
                <ul>
                    <li><a href="/skyvoyager-airline/flights/search.php"><i class="fas fa-search"></i> Flights</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="/skyvoyager-airline/bookings/manage.php"><i class="fas fa-ticket-alt"></i> My Bookings</a></li>
                        <li><a href="/skyvoyager-airline/account/profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <?php else: ?>
                        <li><a href="/skyvoyager-airline/account/login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <?php endif; ?>

                    <!-- Admin Links (Only visible if the user is an admin) -->
                    <?php if(isAdmin()): ?>
                        <li><a href="/skyvoyager-airline/admin/dashboard.php">Admin Dashboard</a></li>
                    <?php endif; ?>

                    <!-- Logout link (Always visible if the user is logged in) -->
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="/skyvoyager-airline/account/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
        <!-- Content -->