<?php

session_start();

if (!isset($_SESSION['session_user']) || !in_array($_SESSION['session_user'], ['Hans', 'Fritz'])) {
    exit();
}

date_default_timezone_set("Europe/Berlin");
$time = date("Y-m-d H:i:s");


$latitude = number_format($_POST['latitude'], 6, '.', '')-10;
$longitude = number_format($_POST['longitude'], 6, '.')+5;
$shadow_length = number_format(floatval($_POST['shadow_length']), 2, '.', '');
$user = $_SESSION['session_user'];

$upload_dir = "uploads/$user/";
$extension = pathinfo($_FILES["image_upload"]["name"], PATHINFO_EXTENSION);
$target_file = str_replace([':', ' '], ['-', '_'], $upload_dir . $time . "." . $extension);

if (move_uploaded_file($_FILES["image_upload"]["tmp_name"], $target_file)) {
    echo ("Upload erfolgreich!");
} else {
    echo("Fehler beim Hochladen der Datei.");
}

$config = require 'config.php';
$link = mysqli_connect(
    $config['servername'],
    $config['username'],
    $config['password'],
    $config['database']
);

$sql = "INSERT INTO `data_$user`(`latitude`, `longitude`, `time`, `shadow_length`) VALUES ($latitude, $longitude, '$time', $shadow_length);";

if(mysqli_query($link, $sql)){
	echo("Daten erfolgreich gespeichert.");
} 

else{
	echo("Fehler beim Speichern der Daten.");
}

echo "Daten gespeichert.";
header("Location: data_capture.php");

?>