<?php
// index.php
// Cars DB - Add, Search, Delete, Subquery

require_once(__DIR__ . "/../config/login.php");

// resto del código


function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

$message = "";
$subq_message = "";

// Load dropdown data
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

// ADD CAR
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

            $message = "Car added successfully.";
        } catch (PDOException $e) {
            $message = "Add failed: " . $e->getMessage();
        }
    } else {
        $message = "Missing info (manufacturer, model, year, type).";
    }
}

// DELETE CAR
if (isset($_POST['delete_car'])) {
    $carid = (int)($_POST['car_id'] ?? 0);

    if ($carid) {
        try {
            $stmtDel = $pdo->prepare("DELETE FROM cars WHERE car_id = ?");
            $stmtDel->execute([$carid]);
            $message = "Deleted (if the ID existed).";
        } catch (PDOException $e) {
            $message = "Delete failed: " . $e->getMessage();
        }
    } else {
        $message = "Missing Car ID.";
    }
}

// SEARCH
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

// SUBQUERY
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
            $subq_message = $r['manufacturer_name'] . ": " . $r['car_count'] . " cars.";
        } else {
            $subq_message = "Manufacturer not found.";
        }
    } else {
        $subq_message = "Missing manufacturer selection.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Cars Database</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body { background: #f6f7fb; }
    .brand-pill {
      font-size: .85rem;
      background: rgba(13,110,253,.08);
      border: 1px solid rgba(13,110,253,.15);
      color: #0d6efd;
      padding: .35rem .6rem;
      border-radius: 999px;
    }
    .card { border: 0; box-shadow: 0 10px 24px rgba(0,0,0,.06); }
    .table thead th { background: #0b1320; color: #fff; position: sticky; top: 0; }
    .table-wrap { max-height: 420px; overflow: auto; border-radius: .75rem; }
    .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
  </style>
</head>

<body>
<div class="container py-4">

  <div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
    <div>
      <h1 class="h3 mb-1">Cars Database</h1>
      <div class="text-secondary">PHP + MariaDB + Docker • Add / Search / Delete / Subquery</div>
    </div>
    <div class="brand-pill">Open to opportunities • Student / Junior</div>
  </div>

  <?php if ($message): ?>
    <div class="alert alert-info mb-3">
      <strong>Info:</strong> <?php echo h($message); ?>
    </div>
  <?php endif; ?>

  <?php if ($subq_message): ?>
    <div class="alert alert-success mb-3">
      <strong>Result:</strong> <?php echo h($subq_message); ?>
    </div>
  <?php endif; ?>

  <div class="row g-3">

    <!-- Add Car -->
    <div class="col-12 col-lg-6">
      <div class="card p-3">
        <h2 class="h5 mb-3">Add Car</h2>

        <form method="POST">
          <div class="mb-2">
            <label class="form-label">Manufacturer</label>
            <select class="form-select" name="manufacturer_id" required>
              <option value="">Select...</option>
              <?php foreach ($manufacturers as $m): ?>
                <option value="<?php echo (int)$m['manufacturer_id']; ?>">
                  <?php echo h($m['manufacturer_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label">Model</label>
            <input class="form-control" type="text" name="model" placeholder="e.g., Civic" required>
          </div>

          <div class="row g-2">
            <div class="col-6">
              <label class="form-label">Year</label>
              <input class="form-control" type="number" name="year" placeholder="2023" required>
            </div>
            <div class="col-6">
              <label class="form-label">Type</label>
              <select class="form-select" name="type_id" required>
                <option value="">Select...</option>
                <?php foreach ($vehicle_types as $t): ?>
                  <option value="<?php echo (int)$t['type_id']; ?>">
                    <?php echo h($t['type_name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="row g-2 mt-1">
            <div class="col-6">
              <label class="form-label">Country (optional)</label>
              <input class="form-control" type="text" name="country_of_origin" placeholder="e.g., Japan">
            </div>
            <div class="col-6">
              <label class="form-label">Price USD (optional)</label>
              <input class="form-control" type="number" step="0.01" name="price_usd" placeholder="e.g., 23000">
            </div>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button class="btn btn-primary" type="submit" name="add_car">Add</button>
            <a class="btn btn-outline-secondary" href="<?php echo h($_SERVER['PHP_SELF']); ?>">Reset</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete + Subquery -->
    <div class="col-12 col-lg-6">
      <div class="card p-3 mb-3">
        <h2 class="h5 mb-3">Delete Car</h2>
        <form method="POST" class="row g-2">
          <div class="col-8">
            <label class="form-label">Car ID</label>
            <input class="form-control" type="number" name="car_id" placeholder="e.g., 12" required>
          </div>
          <div class="col-4 d-flex align-items-end">
            <button class="btn btn-danger w-100" type="submit" name="delete_car">Delete</button>
          </div>
        </form>
        <div class="text-secondary mt-2" style="font-size:.9rem;">
          Tip: You can find the ID in the table below.
        </div>
      </div>

      <div class="card p-3">
        <h2 class="h5 mb-3">Count Cars Per Manufacturer (Subquery)</h2>

        <form method="GET" class="row g-2">
          <div class="col-8">
            <label class="form-label">Manufacturer</label>
            <select class="form-select" name="manufacturer_id">
              <option value="">Select...</option>
              <?php foreach ($manufacturers as $m): ?>
                <option value="<?php echo (int)$m['manufacturer_id']; ?>">
                  <?php echo h($m['manufacturer_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-4 d-flex align-items-end">
            <button class="btn btn-success w-100" type="submit" name="count_cars">Count</button>
          </div>

          <?php if ($q !== ''): ?>
            <input type="hidden" name="q" value="<?php echo h($q); ?>">
          <?php endif; ?>
        </form>
      </div>
    </div>

    <!-- Search + Table -->
    <div class="col-12">
      <div class="card p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-2">
          <div>
            <h2 class="h5 mb-1">Search</h2>
            <div class="text-secondary" style="font-size:.9rem;">Search by manufacturer, model, year, type, country, or price.</div>
          </div>
          <form method="GET" class="d-flex gap-2">
            <input class="form-control" style="min-width: 260px;" type="text" name="q" value="<?php echo h($q); ?>" placeholder="Try: BMW, 2023, SUV...">
            <button class="btn btn-outline-primary" type="submit">Search</button>
            <a class="btn btn-outline-secondary" href="<?php echo h($_SERVER['PHP_SELF']); ?>">Clear</a>
          </form>
        </div>

        <div class="table-wrap mt-3">
          <table class="table table-striped table-hover align-middle mb-0">
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
                  <td><?php echo h($row[5]); ?></td>
                  <td><?php echo h($row[6]); ?></td>
                </tr>
              <?php endwhile; ?>

              <?php if (!$hasRows): ?>
                <tr>
                  <td colspan="7" class="text-center text-secondary py-4">
                    No results. Try a different search.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <div class="text-secondary mt-2" style="font-size:.85rem;">
          <span class="mono">Tip:</span> This project runs locally via Docker Compose. Recruiters/devs can clone the repo and run <span class="mono">docker compose up</span>.
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Bootstrap JS (AL FINAL, correcto) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
