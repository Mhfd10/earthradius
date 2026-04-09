<?php
session_start();
$user = $_SESSION['session_user'] ?? null;

if (!$user || !in_array($user, ['Hans', 'Fritz'])) {
    exit("Zugriff verweigert.");
}

$config = require 'config.php';
$link = mysqli_connect(
    $config['servername'],
    $config['username'],
    $config['password'],
    $config['database']
);

$table = "data_" . $user;
$result = mysqli_query($link, "SELECT * FROM `$table` ORDER BY time DESC");
?>

<!DOCTYPE html>
<html lang="de">

<head>
	<title>
		Datenerfassung
	</title>
	<link rel="stylesheet" media="all" href="cssbase.css" />
	<link rel="stylesheet" media="(min-width: 672px)" href="csswide.css" />	<meta charset="utf-8">	<meta charset="utf-8">
	<link rel="icon" type="image/png" href="favicon.png">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

<div class="main">

<form action="data_collection.php" method="post" enctype="multipart/form-data">
  <ul>
    <label><br />
      <label for="shadow_length">Schattenlänge</label>
      <input id="shadow_length" name="shadow_length" required>
    </label><br /><br />

    <label for="image_upload">Bild hochladen:</label>
    <input type="file" id="image_upload" name="image_upload" accept="image/*"><br /><br />

    <input type="submit" value="Hochladen">
	<input type="hidden" name="latitude" id="latitude">
	<input type="hidden" name="longitude" id="longitude">
  </ul>
</form>

<script>
navigator.geolocation.getCurrentPosition(function(position) {
    document.getElementById('latitude').value = position.coords.latitude;
    document.getElementById('longitude').value = position.coords.longitude;
});
</script>


<hr>

<iframe src="calculate_earthradius.php" width="400" style="border:0;"></iframe><br>

<!-- Data export -->
<form action="data_export.php" method="post" enctype="multipart/form-data">
<button type="submit">Daten exportieren</button>
</form><br>

<!-- Image export -->
<form action="image_export.php" method="post" enctype="multipart/form-data">
<button type="submit">Bilder exportieren</button>
</form>

<!-- Show entries from the database -->
<h2>Erfasste Daten:</h2>
<table border="1" cellpadding="6">
  <tr>
    <th>Zeit</th>
    <th>Breite</th>
    <th>Länge</th>
    <th>Schattenlänge</th>
    <th>Bild</th>
    <th>Bild hochladen</th>
	<th>Löschen</th>
  </tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
  <tr>
    <td><?= htmlspecialchars($row['time']) ?></td>
    <td><?= $row['latitude']+10 ?></td>
    <td><?= $row['longitude']-5 ?></td>
    <td><?= $row['shadow_length'] ?></td>
    <td>
	<?php
	$sanitized_time = str_replace([':', ' '], ['-', '_'], $row['time']);
	$image_path = "uploads/$user/{$sanitized_time}.dng";
	?>
    <?php if (file_exists($image_path)): ?>
		<img src="<?= htmlspecialchars($image_path) ?>" alt="Bild" style="max-height: 100px;">
	<?php else: ?>
		Kein Bild
	<?php endif; ?>
    </td>
    <td>
      <!-- Upload image form if image is missing -->
      <?php if (empty($row['image_name'])): ?>
        <form action="upload_later.php" method="post" enctype="multipart/form-data">
          <input type="hidden" name="time" value="<?= $row['time'] ?>">
          <input type="file" name="image_upload" accept="image/*" required>
          <button type="submit">Bild hochladen</button>
        </form>
      <?php endif; ?>
    </td>
	<td>
		<!-- Delete data and image from the corresponding row -->
		<?php if (empty($row['image_name'])): ?>
		  <form action="delete_data.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="time" value="<?= $row['time'] ?>">
			<button type="submit">Daten löschen</button>
		  </form>
		<?php endif; ?>
	</td>
  </tr>
<?php endwhile; ?>
</table>

</div class="main">
</body>
</html>