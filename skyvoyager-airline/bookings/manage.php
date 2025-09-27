<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit();
}

try {
    $stmt = $conn->prepare("
        SELECT b.*, 
               f.flight_number, f.origin_code, f.destination_code,
               f.departure_utc, f.arrival_utc
        FROM bookings b
        JOIN flight_schedule f ON b.flight_id = f.flight_id
        WHERE b.user_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error loading bookings: " . $e->getMessage());
}

$page_title = "My Bookings";
?>

<div class="bookings-container">
    <h1>My Bookings</h1>

    <?php if(empty($bookings)): ?>
        <div class="no-bookings">
            <i class="fas fa-ticket-alt"></i>
            <p>You haven't made any bookings yet.</p>
            <a href="/skyvoyager-airline/flights/search.php" class="btn">Find Flights</a>
        </div>
    <?php else: ?>
        <div class="bookings-list">
            <?php foreach($bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <span class="booking-id">#<?= $booking['booking_reference'] ?></span>
                        <span class="booking-status <?= strtolower($booking['status']) ?>">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </div>
                    <div class="booking-flight">
                        <div class="flight-route">
                            <span class="airport"><?= $booking['origin_code'] ?></span>
                            <i class="fas fa-long-arrow-alt-right"></i>
                            <span class="airport"><?= $booking['destination_code'] ?></span>
                        </div>
                        <div class="flight-number"><?= $booking['flight_number'] ?></div>
                    </div>
                    <div class="booking-details">
                        <div>
                            <h4>Departure</h4>
                            <p><?= date('M d, Y H:i', strtotime($booking['departure_utc'])) ?></p>
                        </div>
                        <div>
                            <h4>Passengers</h4>
                            <p><?= $booking['passengers'] ?></p>
                        </div>
                        <div>
                            <h4>Total Paid</h4>
                            <p class="price">$<?= number_format($booking['total_price'], 2) ?></p>
                        </div>
                    </div>
                    <div class="booking-actions">
                        <?php if($booking['status'] === 'confirmed'): ?>
                            <a href="#" class="btn btn-cancel">Cancel</a>
                        <?php endif; ?>
                        <a href="/skyvoyager-airline/bookings/view.php?id=<?= $booking['booking_id'] ?>" class="btn">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
