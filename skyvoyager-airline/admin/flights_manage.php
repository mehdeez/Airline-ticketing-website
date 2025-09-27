<?php
// Secure admin access
require_once '../includes/config.php';
session_start();

if (!isAdmin()) {
    header("Location: ../account/login.php?error=admin_only");
    exit();
}

// Handle flight deletion
if (isset($_GET['delete'])) {
    $flight_id = (int)$_GET['delete'];
    try {
        $conn->beginTransaction();
        
        // Check for existing bookings
        $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE flight_id = ?");
        $stmt->execute([$flight_id]);
        
        if ($stmt->fetchColumn() > 0) {
            // Deactivate instead of delete
            $conn->prepare("UPDATE flight_schedule SET is_active = FALSE WHERE flight_id = ?")
                 ->execute([$flight_id]);
        } else {
            // Delete if no bookings
            $conn->prepare("DELETE FROM flight_schedule WHERE flight_id = ?")
                 ->execute([$flight_id]);
        }
        
        $conn->commit();
        $_SESSION['flash'] = "Flight removed successfully";
        header("Location: flights_manage.php");
        exit();
    } catch(PDOException $e) {
        $conn->rollBack();
        die("Error deleting flight");
    }
}

// Handle flight additions/edits
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_id = $_POST['flight_id'] ?? null;
    $data = [
        'airline_code' => $_POST['airline_code'],
        'flight_number' => $_POST['flight_number'],
        'origin_code' => $_POST['origin_code'],
        'destination_code' => $_POST['destination_code'],
        'departure_utc' => $_POST['departure_date'] . ' ' . $_POST['departure_time'],
        'arrival_utc' => $_POST['arrival_date'] . ' ' . $_POST['arrival_time'],
        'aircraft_type' => $_POST['aircraft_type'],
        'base_price' => $_POST['base_price'],
        'seats_total' => $_POST['seats_total'],
        'seats_remaining' => $_POST['seats_remaining']
    ];
    
    try {
        if ($flight_id) {
            // Update existing flight
            $stmt = $conn->prepare("
                UPDATE flight_schedule 
                SET airline_code = :airline_code,
                    flight_number = :flight_number,
                    origin_code = :origin_code,
                    destination_code = :destination_code,
                    departure_utc = :departure_utc,
                    arrival_utc = :arrival_utc,
                    aircraft_type = :aircraft_type,
                    base_price = :base_price,
                    seats_total = :seats_total,
                    seats_remaining = :seats_remaining
                WHERE flight_id = :flight_id
            ");
            $data['flight_id'] = $flight_id;
        } else {
            // Insert new flight
            $stmt = $conn->prepare("
                INSERT INTO flight_schedule 
                (airline_code, flight_number, origin_code, destination_code, 
                 departure_utc, arrival_utc, aircraft_type, base_price, 
                 seats_total, seats_remaining)
                VALUES 
                (:airline_code, :flight_number, :origin_code, :destination_code,
                 :departure_utc, :arrival_utc, :aircraft_type, :base_price,
                 :seats_total, :seats_remaining)
            ");
        }
        
        $stmt->execute($data);
        $_SESSION['flash'] = "Flight " . ($flight_id ? "updated" : "added") . " successfully";
        header("Location: flights_manage.php");
        exit();
    } catch(PDOException $e) {
        die("Error saving flight: " . $e->getMessage());
    }
}

// Get all flights
$flights = $conn->query("
    SELECT *, 
           DATE_FORMAT(departure_utc, '%Y-%m-%d') AS departure_date,
           DATE_FORMAT(departure_utc, '%H:%i') AS departure_time,
           DATE_FORMAT(arrival_utc, '%Y-%m-%d') AS arrival_date,
           DATE_FORMAT(arrival_utc, '%H:%i') AS arrival_time
    FROM flight_schedule
    ORDER BY departure_utc DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Get flight to edit (if specified)
$edit_flight = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    foreach ($flights as $flight) {
        if ($flight['flight_id'] === $edit_id) {
            $edit_flight = $flight;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Flights | SkyVoyager</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .flight-form {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-purple);
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--light-purple);
            border-radius: 4px;
        }
        .flight-table {
            width: 100%;
            border-collapse: collapse;
        }
        .flight-table th {
            background: var(--light-purple);
            padding: 0.75rem;
            text-align: left;
        }
        .flight-table td {
            padding: 0.75rem;
            border-bottom: 1px solid var(--light-purple);
        }
        .status-active {
            color: green;
        }
        .status-inactive {
            color: #999;
        }
    </style>
</head>
<body>
    <?php require_once '../includes/header.php'; ?>

    <main class="main-container">
        <h1 class="page-title">Manage Flights</h1>
        
        <div class="admin-nav">
            <a href="dashboard.php" class="btn">Dashboard</a>
            <a href="flights_manage.php" class="btn btn-primary">Manage Flights</a>
        </div>

        <?php if (isset($_SESSION['flash'])): ?>
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                <?= $_SESSION['flash'] ?>
                <?php unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>

        <div class="flight-form">
            <h2 style="color: var(--primary-purple); margin-top: 0;">
                <?= $edit_flight ? 'Edit Flight' : 'Add New Flight' ?>
            </h2>
            
            <form method="POST">
                <?php if ($edit_flight): ?>
                    <input type="hidden" name="flight_id" value="<?= $edit_flight['flight_id'] ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Airline Code</label>
                        <input type="text" name="airline_code" class="form-control" 
                               value="<?= $edit_flight['airline_code'] ?? 'SV' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Flight Number</label>
                        <input type="text" name="flight_number" class="form-control" 
                               value="<?= $edit_flight['flight_number'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Origin Airport</label>
                        <input type="text" name="origin_code" class="form-control" 
                               value="<?= $edit_flight['origin_code'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Destination Airport</label>
                        <input type="text" name="destination_code" class="form-control" 
                               value="<?= $edit_flight['destination_code'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Departure Date</label>
                        <input type="date" name="departure_date" class="form-control" 
                               value="<?= $edit_flight['departure_date'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Departure Time</label>
                        <input type="time" name="departure_time" class="form-control" 
                               value="<?= $edit_flight['departure_time'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Arrival Date</label>
                        <input type="date" name="arrival_date" class="form-control" 
                               value="<?= $edit_flight['arrival_date'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Arrival Time</label>
                        <input type="time" name="arrival_time" class="form-control" 
                               value="<?= $edit_flight['arrival_time'] ?? '' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Aircraft Type</label>
                        <input type="text" name="aircraft_type" class="form-control" 
                               value="<?= $edit_flight['aircraft_type'] ?? '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Base Price ($)</label>
                        <input type="number" step="0.01" name="base_price" class="form-control" 
                               value="<?= $edit_flight['base_price'] ?? '199.99' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Total Seats</label>
                        <input type="number" name="seats_total" class="form-control" 
                               value="<?= $edit_flight['seats_total'] ?? '180' ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Seats Remaining</label>
                        <input type="number" name="seats_remaining" class="form-control" 
                               value="<?= $edit_flight['seats_remaining'] ?? '180' ?>" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <?= $edit_flight ? 'Update Flight' : 'Add Flight' ?>
                </button>
                
                <?php if ($edit_flight): ?>
                    <a href="flights_manage.php" class="btn">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <h2 style="color: var(--primary-purple);">All Flights</h2>
        <table class="flight-table">
            <thead>
                <tr>
                    <th>Flight #</th>
                    <th>Route</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Seats</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($flights as $flight): ?>
                <tr>
                    <td><?= $flight['airline_code'] . $flight['flight_number'] ?></td>
                    <td><?= $flight['origin_code'] ?> → <?= $flight['destination_code'] ?></td>
                    <td><?= date('M d, Y H:i', strtotime($flight['departure_utc'])) ?></td>
                    <td><?= date('M d, Y H:i', strtotime($flight['arrival_utc'])) ?></td>
                    <td><?= $flight['seats_remaining'] ?>/<?= $flight['seats_total'] ?></td>
                    <td>$<?= number_format($flight['base_price'], 2) ?></td>
                    <td class="<?= $flight['is_active'] ? 'status-active' : 'status-inactive' ?>">
                        <?= $flight['is_active'] ? 'Active' : 'Inactive' ?>
                    </td>
                    <td>
                        <a href="flights_manage.php?edit=<?= $flight['flight_id'] ?>" class="btn btn-sm">Edit</a>
                        <a href="flights_manage.php?delete=<?= $flight['flight_id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>