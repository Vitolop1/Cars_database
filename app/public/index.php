<?php
/**
 * Cars Database - Main Application
 * PHP + MariaDB + Docker Demo
 * Features: Add, Search, Delete, Subquery
 */

require_once(__DIR__ . "/config/login.php");

// Función helper para escapar HTML
function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

$message = "";
$subq_message = "";

// Cargar datos para dropdowns
$manufacturers = $pdo->query("
    SELECT manufacturer_id, manufacturer_name
    FROM manufacturers
    ORDER BY manufacturer_name
")->fetchAll();

$vehicle_types = $pdo->query("
    SELECT type_id, type_name
    FROM vehicle_types
    ORDER BY type_name
")->fetchAll();

// ============================================
// ADD CAR
// ============================================
if (isset($_POST['add_car'])) {
    $manu  = (int)($_POST['manufacturer_id'] ?? 0);
    $model = trim($_POST['model'] ?? "");
    $year  = (int)($_POST['year'] ?? 0);
    $type  = (int)($_POST['type_id'] ?? 0);
    $country = trim($_POST['country_of_origin'] ?? "");
    $price   = trim($_POST['price_usd'] ?? "");

    if ($manu && $model !== "" && $year && $type) {
        try {
            $stmtAdd = $pdo->prepare("
                INSERT INTO cars (manufacturer_id, model, year, type_id, country_of_origin, price_usd)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmtAdd->execute([
                $manu,
                $model,
                $year,
                $type,
                ($country !== "" ? $country : null),
                ($price !== "" ? $price : null)
            ]);

            $message = "✅ Car added successfully!";
        } catch (PDOException $e) {
            $message = "❌ Add failed: " . $e->getMessage();
        }
    } else {
        $message = "⚠️ Missing required information (manufacturer, model, year, type).";
    }
}

// ============================================
// DELETE CAR
// ============================================
if (isset($_POST['delete_car'])) {
    $carid = (int)($_POST['car_id'] ?? 0);

    if ($carid) {
        try {
            $stmtDel = $pdo->prepare("DELETE FROM cars WHERE car_id = ?");
            $stmtDel->execute([$carid]);
            $rowsAffected = $stmtDel->rowCount();
            
            if ($rowsAffected > 0) {
                $message = "✅ Car deleted successfully (ID: $carid)";
            } else {
                $message = "⚠️ No car found with ID: $carid";
            }
        } catch (PDOException $e) {
            $message = "❌ Delete failed: " . $e->getMessage();
        }
    } else {
        $message = "⚠️ Missing Car ID.";
    }
}

// ============================================
// SEARCH
// ============================================
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$qLike = "%$q%";

$stmt = $pdo->prepare("
    SELECT c.car_id, m.manufacturer_name, c.model, c.year,
           t.type_name, c.country_of_origin, c.price_usd
    FROM cars c
    JOIN manufacturers m ON c.manufacturer_id = m.manufacturer_id
    JOIN vehicle_types t ON c.type_id = t.type_id
    WHERE ? = ''
       OR m.manufacturer_name LIKE ?
       OR c.model LIKE ?
       OR CAST(c.year AS CHAR) LIKE ?
       OR t.type_name LIKE ?
       OR c.country_of_origin LIKE ?
       OR CAST(c.price_usd AS CHAR) LIKE ?
    ORDER BY c.car_id
");
$stmt->execute([$q, $qLike, $qLike, $qLike, $qLike, $qLike, $qLike]);

// ============================================
// SUBQUERY - Count Cars per Manufacturer
// ============================================
if (isset($_GET['count_cars'])) {
    $manu_id = (int)($_GET['manufacturer_id'] ?? 0);

    if ($manu_id) {
        $stmt2 = $pdo->prepare("
            SELECT m.manufacturer_name,
                   (SELECT COUNT(*) FROM cars c WHERE c.manufacturer_id = m.manufacturer_id) AS car_count
            FROM manufacturers m
            WHERE m.manufacturer_id = ?
        ");
        $stmt2->execute([$manu_id]);
        $r = $stmt2->fetch();

        if ($r) {
            $subq_message = "📊 " . $r['manufacturer_name'] . " has " . $r['car_count'] . " car(s) in the database.";
        } else {
            $subq_message = "⚠️ Manufacturer not found.";
        }
    } else {
        $subq_message = "⚠️ Please select a manufacturer.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Cars Database | PHP + MariaDB + Docker</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style.css">
</head>

<body>
<div class="container">

  <!-- Header -->
  <div class="header">
    <div>
      <h1>🚗 Cars Database</h1>
      <div class="subtitle">PHP + MariaDB + Docker • CRUD Operations + Subqueries</div>
    </div>
    <div class="badges">
      <span class="badge">🐳 Dockerized</span>
      <span class="badge">💼 Open to Opportunities</span>
    </div>
  </div>

  <!-- Messages -->
  <?php if ($message): ?>
    <div class="alert <?php echo (strpos($message, '✅') !== false) ? 'ok' : 'bad'; ?>">
      <?php echo h($message); ?>
    </div>
  <?php endif; ?>

  <?php if ($subq_message): ?>
    <div class="alert ok">
      <?php echo h($subq_message); ?>
    </div>
  <?php endif; ?>

  <!-- Main Grid -->
  <div class="grid">

    <!-- Add Car Form -->
    <div class="card">
      <h2>➕ Add Car</h2>

      <form method="POST">
        <label>Manufacturer</label>
        <select name="manufacturer_id" required>
          <option value="">Select manufacturer...</option>
          <?php foreach ($manufacturers as $m): ?>
            <option value="<?php echo (int)$m['manufacturer_id']; ?>">
              <?php echo h($m['manufacturer_name']); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Model</label>
        <input type="text" name="model" placeholder="e.g., Civic" required>

        <div class="row">
          <div>
            <label>Year</label>
            <input type="number" name="year" placeholder="2023" min="1886" max="2100" required>
          </div>
          <div>
            <label>Type</label>
            <select name="type_id" required>
              <option value="">Select type...</option>
              <?php foreach ($vehicle_types as $t): ?>
                <option value="<?php echo (int)$t['type_id']; ?>">
                  <?php echo h($t['type_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="row">
          <div>
            <label>Country (optional)</label>
            <input type="text" name="country_of_origin" placeholder="e.g., Japan">
          </div>
          <div>
            <label>Price USD (optional)</label>
            <input type="number" step="0.01" name="price_usd" placeholder="e.g., 23000">
          </div>
        </div>

        <div class="actions">
          <button type="submit" name="add_car">Add Car</button>
          <button type="button" class="secondary" onclick="window.location.href='<?php echo h($_SERVER['PHP_SELF']); ?>'">Reset</button>
        </div>
      </form>
    </div>

    <!-- Delete Car Form -->
    <div class="card">
      <h2>🗑️ Delete Car</h2>
      <form method="POST">
        <label>Car ID</label>
        <input type="number" name="car_id" placeholder="e.g., 12" required>
        
        <div class="actions">
          <button type="submit" name="delete_car" class="danger">Delete</button>
        </div>
      </form>
      <small class="muted">💡 Tip: Find the ID in the table below</small>
    </div>

    <!-- Subquery Form -->
    <div class="card">
      <h2>📊 Count Cars Per Manufacturer (Subquery)</h2>

      <form method="GET">
        <label>Select Manufacturer</label>
        <select name="manufacturer_id">
          <option value="">Choose manufacturer...</option>
          <?php foreach ($manufacturers as $m): ?>
            <option value="<?php echo (int)$m['manufacturer_id']; ?>">
              <?php echo h($m['manufacturer_name']); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <div class="actions">
          <button type="submit" name="count_cars">Count Cars</button>
        </div>

        <?php if ($q !== ''): ?>
          <input type="hidden" name="q" value="<?php echo h($q); ?>">
        <?php endif; ?>
      </form>
    </div>

    <!-- Search Form -->
    <div class="card">
      <h2>🔍 Search Cars</h2>
      
      <form method="GET">
        <label>Search by any field</label>
        <input type="text" name="q" value="<?php echo h($q); ?>" 
               placeholder="Try: BMW, 2023, SUV, Germany...">
        
        <div class="actions">
          <button type="submit">Search</button>
          <button type="button" class="secondary" 
                  onclick="window.location.href='<?php echo h($_SERVER['PHP_SELF']); ?>'">Clear</button>
        </div>
      </form>
      <small class="muted">💡 Searches across manufacturer, model, year, type, country, and price</small>
    </div>

  </div>

  <!-- Results Table -->
  <div class="card" style="margin-top: 14px;">
    <h2>📋 Cars in Database</h2>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:70px;">ID</th>
            <th>Manufacturer</th>
            <th>Model</th>
            <th style="width:90px;">Year</th>
            <th>Type</th>
            <th>Country</th>
            <th style="width:140px;">Price (USD)</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $hasRows = false;
            while ($row = $stmt->fetch(PDO::FETCH_NUM)):
              $hasRows = true;
          ?>
            <tr>
              <td class="mono"><?php echo h($row[0]); ?></td>
              <td><?php echo h($row[1]); ?></td>
              <td><?php echo h($row[2]); ?></td>
              <td><?php echo h($row[3]); ?></td>
              <td><?php echo h($row[4]); ?></td>
              <td><?php echo h($row[5] ?? '-'); ?></td>
              <td><?php echo $row[6] ? '$' . number_format($row[6], 2) : '-'; ?></td>
            </tr>
          <?php endwhile; ?>

          <?php if (!$hasRows): ?>
            <tr>
              <td colspan="7" style="text-align:center; padding: 30px; color: var(--muted);">
                🔍 No results found. <?php echo $q ? 'Try a different search term.' : 'Add some cars to get started!'; ?>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="footer">
      <strong>💻 Tech Stack:</strong> PHP 8.2 • MariaDB 10.4 • Docker Compose • PDO • Prepared Statements
      <br>
      <strong>🔗 GitHub:</strong> Clone this repo and run <span class="mono">docker compose up</span> to get started!
    </div>
  </div>

</div>
</body>
</html>
