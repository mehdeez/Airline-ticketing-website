<?php
session_start();
require_once '../../includes/config.php';

// Unique seat selection feature
if (isset($_GET['flight_id'])) {
    $flightId = (int)$_GET['flight_id'];
    try {
        $stmt = $conn->prepare("SELECT * FROM flight_schedule WHERE flight_id = ?");
        $stmt->execute([$flightId]);
        $flight = $stmt->fetch();
        
        if (!$flight) {
            // Flight not found, redirect to the search page with a message
            header("Location: ../flights/search.php?error=flight_not_found");
            exit();
        }

        // Calculate dynamic price
        $finalPrice = calculateDynamicPrice($flight['base_price'], $flight['seats_remaining'], 180);

    } catch(PDOException $e) {
        die("System error. Try again later.");
    }
} else {
    header("Location: ../flights/search.php?error=invalid_flight");
    exit();
}

?>

<!-- HTML for showing flight details and booking form -->
<div class="flight-booking">
    <h1>Book Flight <?= $flight['flight_number'] ?></h1>
    <div class="flight-info">
        <p>Origin: <?= $flight['origin_code'] ?> - Destination: <?= $flight['destination_code'] ?></p>
        <p>Departure: <?= date('M d, Y H:i', strtotime($flight['departure_utc'])) ?></p>
        <p>Seats Available: <?= $flight['seats_remaining'] ?></p>
        <p>Price: $<?= number_format($finalPrice, 2) ?></p>
    </div>

    <form action="process_booking.php" method="POST">
        <input type="hidden" name="flight_id" value="<?= $flight['flight_id'] ?>">

        <label for="passengers">Number of Passengers:</label>
        <input type="number" name="passengers" min="1" max="<?= $flight['seats_remaining'] ?>" required>

        <label for="seats">Select Seat:</label>
        <select name="seat" required>
            <?php for ($i = 1; $i <= $flight['seats_remaining']; $i++): ?>
                <option value="<?= $i ?>">Seat <?= $i ?></option>
            <?php endfor; ?>
        </select>

        <p>Total Price: $<?= number_format($finalPrice, 2) ?></p>

        <button type="submit" class="btn">Confirm Booking</button>
    </form>
</div>
