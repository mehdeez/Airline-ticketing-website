<?php
require_once '../includes/config.php';
require_once '../includes/header.php';

$flight_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    $stmt = $conn->prepare("
        SELECT *, 
               TIMESTAMPDIFF(HOUR, departure_utc, arrival_utc) AS duration_hours,
               DATE_FORMAT(departure_utc, '%a, %b %e, %Y') AS departure_date,
               DATE_FORMAT(departure_utc, '%h:%i %p') AS departure_time,
               DATE_FORMAT(arrival_utc, '%a, %b %e, %Y') AS arrival_date,
               DATE_FORMAT(arrival_utc, '%h:%i %p') AS arrival_time
        FROM flight_schedule
        WHERE flight_id = ? AND is_active = TRUE
    ");
    $stmt->execute([$flight_id]);
    $flight = $stmt->fetch();
    
    if (!$flight) {
        header("Location: search.php");
        exit();
    }
} catch(PDOException $e) {
    die("Error loading flight details");
}

$page_title = $flight['airline_code'] . $flight['flight_number'] . " Details";
?>

<div class="flight-details">
    <div class="flight-header">
        <h1><?= $flight['airline_code'] . $flight['flight_number'] ?></h1>
        <div class="route">
            <span class="airport"><?= $flight['origin_code'] ?></span>
            <i class="fas fa-long-arrow-alt-right"></i>
            <span class="airport"><?= $flight['destination_code'] ?></span>
        </div>
    </div>

    <div class="flight-timings">
        <div class="timing-block">
            <h3>Departure</h3>
            <p class="date"><?= $flight['departure_date'] ?></p>
            <p class="time"><?= $flight['departure_time'] ?></p>
        </div>
        <div class="duration">
            <i class="fas fa-clock"></i>
            <span><?= $flight['duration_hours'] ?>h</span>
        </div>
        <div class="timing-block">
            <h3>Arrival</h3>
            <p class="date"><?= $flight['arrival_date'] ?></p>
            <p class="time"><?= $flight['arrival_time'] ?></p>
        </div>
    </div>

    <div class="flight-info">
        <div class="info-card">
            <h3>Aircraft</h3>
            <p><?= $flight['aircraft_type'] ?? 'Boeing 737' ?></p>
        </div>
        <div class="info-card">
            <h3>Available Seats</h3>
            <p><?= $flight['seats_remaining'] ?> / <?= $flight['seats_total'] ?></p>
        </div>
        <div class="info-card">
            <h3>Price</h3>
            <p class="price">$<?= number_format($flight['base_price'], 2) ?></p>
        </div>
    </div>

    <?php if(isset($_SESSION['user_id'])): ?>
        <a href="/skyvoyager-airline/bookings/create.php?flight_id=<?= $flight_id ?>" class="btn btn-book">Book Now</a>
    <?php else: ?>
        <div class="login-prompt">
            <p>Please <a href="/skyvoyager-airline/account/login.php">login</a> to book this flight</p>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>