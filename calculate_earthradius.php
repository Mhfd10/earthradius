<?php
$config = require 'config.php';
$link = mysqli_connect(
    $config['servername'],
    $config['username'],
    $config['password'],
    $config['database']
);

if (!$link) exit("DB-Fehler");

$hoehe_Hans = 5;
$hoehe_Fritz = 5.4;
$earthradius = 6371;

$hans_result = mysqli_query($link, "SELECT shadow_length, latitude, longitude FROM data_hans ORDER BY time ASC");
$fritz_result = mysqli_query($link, "SELECT shadow_length, latitude, longitude FROM data_fritz ORDER BY time ASC");

$radien = [];
$sum = 0;
$count = 0;

while (($hans = mysqli_fetch_assoc($hans_result)) && ($fritz = mysqli_fetch_assoc($fritz_result))) {
    $s_hans = (float) $hans['shadow_length'];
    $s_fritz = (float) $fritz['shadow_length'];

    $alpha_h = atan($hoehe_Hans / $s_hans);
    $alpha_f = atan($hoehe_Fritz / $s_fritz);
    $delta_alpha = abs($alpha_h - $alpha_f);

    if ($delta_alpha < 1e-6) continue;

    $lat1 = (float)$hans['latitude'];
    $lat2 = (float)$fritz['latitude'];
    $delta_lambda = deg2rad($lat2 - $lat1);

    $radius = ($delta_lambda * $earthradius) / $delta_alpha;

    $radien[] = $radius;
    $sum += $radius;
    $count++;
}

if ($count > 1) {
    $mittelwert = $sum / $count;

    // Quadratische Abweichung aufsummieren
    $abweichung_summe = 0;
    foreach ($radien as $r) {
        $abweichung_summe += pow($r - $mittelwert, 2);
    }

    $standardabweichung = sqrt($abweichung_summe / $count);
	$abweichung_zu_lit = 100 * ($mittelwert - $earthradius) / $earthradius;
	$abweichung_sigma = ($mittelwert - $earthradius) / $standardabweichung;
	$CV = 100 * $standardabweichung / $mittelwert;
	
	echo "Mittelwert = (" . round($mittelwert, 2) . " ± " . round($standardabweichung, 2) . ") km <br>";
	echo "Δ<sub>T</sub> = " . round($abweichung_zu_lit, 2) . " % <br>";
	echo "Δ<sub>σ</sub> = " . round($abweichung_sigma, 2) . "<br>";
	echo "Variationskoeffizient = " . round($CV, 2) . " %";
}
else if ($count == 1) {
	echo "Erdradius: " . round($radius, 2) . " km<br>";
	$abweichung_zu_lit = 100 * ($radius - $earthradius) / $earthradius;
	echo "Δ<sub>T</sub> = " . round($abweichung_zu_lit, 2) . " %";
}	
else {
    echo "Nicht genügend gültige Datenpaare für Statistik.";
}


mysqli_close($link);
?>
