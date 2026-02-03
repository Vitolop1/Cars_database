<?php
// index.php
// Cars DB - Add, Search, Delete, Subquery

require_once("login.php");

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

        $stmt = $pdo->prepare("
            INSERT INTO cars (manufacturer_id, model, year, type_id, country_of_origin, price_usd)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $manu,
            $model,
            $year,
            $type,
            ($country !== "" ? $country : null),
            ($price !== "" ? $price : null)
        ]);

        $message = "Car added.";
    } else {
        $message = "Missing info.";
    }
}

// DELETE CAR
if (isset($_POST['delete_car'])) {

    $carid = (int)($_POST['car_id'] ?? 0);

    if ($carid) {
        $stmt = $pdo->prepare("DELETE FROM cars WHERE car_id = ?");
        $stmt->execute([$carid]);
        $message = "Deleted.";
    } else {
        $message = "Missing info.";
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
            $subq_message = "Not found.";
        }
    } else {
        $subq_message = "Missing info.";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Cars Database</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<h2>Cars</h2>

<?php if ($message): ?>
  <p><b><?php echo h($message); ?></b></p>
<?php endif; ?>

<h3>Add Car</h3>
<form method="POST">
  Manufacturer:<br>
  <select name="manufacturer_id" required>
    <option value="">Select...</option>
    <?php foreach ($manufacturers as $m): ?>
      <option value="<?php echo (int)$m['manufacturer_id']; ?>">
        <?php echo h($m['manufacturer_name']); ?>
      </option>
    <?php endforeach; ?>
  </select><br><br>

  Model:<br>
  <input type="text" name="model" required><br><br>

  Year:<br>
  <input type="number" name="year" required><br><br>

  Type:<br>
  <select name="type_id" required>
    <option value="">Select...</option>
    <?php foreach ($vehicle_types as $t): ?>
      <option value="<?php echo (int)$t['type_id']; ?>">
        <?php echo h($t['type_name']); ?>
      </option>
    <?php endforeach; ?>
  </select><br><br>

  Country:<br>
  <input type="text" name="country_of_origin"><br><br>

  Price (USD):<br>
  <input type="number" step="0.01" name="price_usd"><br><br>

  <input type="submit" name="add_car" value="Add">
</form>

<hr>

<h3>Search</h3>
<form method="GET">
  <input type="text" name="q" value="<?php echo h($q); ?>">
  <input type="submit" value="Search">
</form>

<br>

<table border="1" cellpadding="4">
  <tr>
    <th>ID</th><th>Manufacturer</th><th>Model</th><th>Year</th>
    <th>Type</th><th>Country</th><th>Price</th>
  </tr>
  <?php while ($row = $stmt->fetch(PDO::FETCH_NUM)): ?>
    <tr>
      <?php foreach ($row as $val): ?>
        <td><?php echo h($val); ?></td>
      <?php endforeach; ?>
    </tr>
  <?php endwhile; ?>
</table>

<hr>

<h3>Delete Car</h3>
<form method="POST">
  Car ID:<br>
  <input type="number" name="car_id" required><br><br>
  <input type="submit" name="delete_car" value="Delete">
</form>

<hr>

<h3>Count Cars Per Manufacturer</h3>
<form method="GET">
  <select name="manufacturer_id">
    <option value="">Select...</option>
    <?php foreach ($manufacturers as $m): ?>
      <option value="<?php echo (int)$m['manufacturer_id']; ?>">
        <?php echo h($m['manufacturer_name']); ?>
      </option>
    <?php endforeach; ?>
  </select><br><br>
  <input type="submit" name="count_cars" value="Count">
</form>

<?php if ($subq_message): ?>
  <p><b><?php echo h($subq_message); ?></b></p>
<?php endif; ?>

</body>
</html>
