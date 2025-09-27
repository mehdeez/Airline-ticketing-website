<?php
// Secure admin access
require_once '../includes/config.php';
session_start();

if (!isAdmin()) {
    header("Location: ../account/login.php?error=admin_only");
    exit();
}

// Get stats
try {
    $bookings = $conn->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
    $flights = $conn->query("SELECT COUNT(*) FROM flight_schedule")->fetchColumn();
    $users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $revenue = $conn->query("SELECT SUM(total_price) FROM bookings WHERE status = 'confirmed'")->fetchColumn();
} catch(PDOException $e) {
    die("Error loading dashboard data");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SkyVoyager</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-top: 4px solid var(--primary-purple);
        }
        .stat-card h3 {
            margin-top: 0;
            color: var(--dark-purple);
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-purple);
        }
        .recent-table {
            width: 100%;
            border-collapse: collapse;
        }
        .recent-table th {
            background: var(--light-purple);
            padding: 0.75rem;
            text-align: left;
        }
        .recent-table td {
            padding: 0.75rem;
            border-bottom: 1px solid var(--light-purple);
        }
    </style>
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <main class="main-container">
        <h1 class="page-title">Admin Dashboard</h1>
        
        <div class="admin-nav">
            <a href="dashboard.php" class="btn btn-primary">Dashboard</a>
            <a href="flights_manage.php" class="btn">Manage Flights</a>
        </div>

        <div class="admin-stats">
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <div class="stat-value"><?= number_format($bookings) ?></div>
            </div>
            <div class="stat-card">
                <h3>Active Flights</h3>
                <div class="stat-value"><?= number_format($flights) ?></div>
            </div>
            <div class="stat-card">
                <h3>Registered Users</h3>
                <div class="stat-value"><?= number_format($users) ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Revenue</h3>
                <div class="stat-value">$<?= number_format($revenue ?? 0, 2) ?></div>
            </div>
        </div>

        <div class="recent-section">
            <h2 style="color: var(--primary-purple);">Recent Bookings</h2>
            <table class="recent-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Flight</th>
                        <th>User</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("
                        SELECT b.booking_id, b.booking_date, b.total_price, b.status,
                               f.flight_number, f.origin_code, f.destination_code,
                               u.username
                        FROM bookings b
                        JOIN flight_schedule f ON b.flight_id = f.flight_id
                        JOIN users u ON b.user_id = u.user_id
                        ORDER BY b.booking_date DESC
                        LIMIT 5
                    ");
                    while ($row = $stmt->fetch()):
                    ?>
                    <tr>
                        <td>#<?= $row['booking_id'] ?></td>
                        <td><?= $row['flight_number'] ?> (<?= $row['origin_code'] ?>-<?= $row['destination_code'] ?>)</td>
                        <td><?= $row['username'] ?></td>
                        <td><?= date('M d, Y', strtotime($row['booking_date'])) ?></td>
                        <td>$<?= number_format($row['total_price'], 2) ?></td>
                        <td>
                            <span style="
                                background: <?= $row['status'] === 'confirmed' ? 'var(--light-purple)' : '#f8d7da' ?>;
                                color: <?= $row['status'] === 'confirmed' ? 'var(--dark-purple)' : '#721c24' ?>;
                                padding: 0.25rem 0.5rem;
                                border-radius: 4px;
                                font-size: 0.85rem;
                            ">
                                <?= ucfirst($row['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>