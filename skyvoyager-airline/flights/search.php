<?php 
require_once '../includes/config.php';
require_once '../includes/header.php';

function calculateDynamicPrice($basePrice, $seatsLeft, $totalSeats) {
    $demandFactor = 1.0 + (0.7 * (1 - ($seatsLeft / $totalSeats)));
    return round($basePrice * $demandFactor, 2);
}

$flights = [];

$airports = ['JFK', 'LAX', 'ORD', 'ATL', 'DFW', 'DEN', 'SFO', 'SEA'];
$airlines = ['SV' => 'SkyVoyager', 'AA' => 'American Airlines', 'DL' => 'Delta', 'UA' => 'United'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $departure = $_POST['departure'] ?? '';
    $arrival = $_POST['arrival'] ?? '';
    $date = $_POST['date'] ?? '';
    $airline = $_POST['airline'] ?? '';
    $nonStopOnly = isset($_POST['non_stop']);
    $passengers = (int) ($_POST['passengers'] ?? 1);

    try {
        $query = "SELECT *, TIMESTAMPDIFF(MINUTE, departure_utc, arrival_utc) AS duration 
                  FROM flight_schedule 
                  WHERE origin_code = ? 
                  AND destination_code = ? 
                  AND DATE(departure_utc) = ? 
                  AND seats_remaining >= ?";
        $params = [$departure, $arrival, $date, $passengers];

        if (!empty($airline)) {
            $query .= " AND airline_code = ?";
            $params[] = $airline;
        }

        if ($nonStopOnly) {
            $query .= " AND duration_minutes <= 300"; // Non-stop flight filter (ensure duration is calculated correctly)
        }

        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $flights = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch(PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}
?>

<main class="container">
    <h1>Search Flights</h1>

    <form method="POST" action="search.php" class="flight-search-form">
        <label for="departure">Departure Airport:</label>
        <select name="departure" required>
            <option value="">-- Select Departure --</option>
            <?php foreach ($airports as $code): ?>
                <option value="<?= $code ?>" <?= ($departure ?? '') === $code ? 'selected' : '' ?>><?= $code ?></option>
            <?php endforeach; ?>
        </select>

        <label for="arrival">Arrival Airport:</label>
        <select name="arrival" required>
            <option value="">-- Select Arrival --</option>
            <?php foreach ($airports as $code): ?>
                <option value="<?= $code ?>" <?= ($arrival ?? '') === $code ? 'selected' : '' ?>><?= $code ?></option>
            <?php endforeach; ?>
        </select>

        <label for="date">Departure Date:</label>
        <input type="date" name="date" required value="<?= $date ?? '' ?>">

        <label for="airline">Airline:</label>
        <select name="airline">
            <option value="">-- Any Airline --</option>
            <?php foreach ($airlines as $code => $name): ?>
                <option value="<?= $code ?>" <?= ($airline ?? '') === $code ? 'selected' : '' ?>><?= $name ?></option>
            <?php endforeach; ?>
        </select>

        <label for="passengers">Passengers:</label>
        <input type="number" name="passengers" min="1" max="10" value="<?= $passengers ?? 1 ?>">

        <label>
            <input type="checkbox" name="non_stop" <?= isset($nonStopOnly) && $nonStopOnly ? 'checked' : '' ?>>
            Non-stop only
        </label>

        <button type="submit">Search Flights</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <h2>Available Flights</h2>
        <?php if (!empty($flights)): ?>
            <table class="recent-table">
                <thead>
                    <tr>
                        <th>Flight</th>
                        <th>From → To</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                        <th>Duration</th>
                        <th>Seats Left</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flights as $flight): ?>
                        <tr>
                            <td><?= $flight['airline_code'] ?> <?= $flight['flight_number'] ?></td>
                            <td><?= $flight['origin_code'] ?> → <?= $flight['destination_code'] ?></td>
                            <td><?= date('M d, H:i', strtotime($flight['departure_utc'])) ?></td>
                            <td><?= date('M d, H:i', strtotime($flight['arrival_utc'])) ?></td>
                            <td><?= $flight['duration'] ?> mins</td>
                            <td><?= $flight['seats_remaining'] ?></td>
                            <td>$<?= calculateDynamicPrice($flight['base_price'], $flight['seats_remaining'], $flight['seats_total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No matching flights found.</p>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php require_once '../includes/footer.php'; ?>
