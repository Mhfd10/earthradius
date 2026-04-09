<?php

session_start();

if (!isset($_SESSION['session_user']) || !in_array($_SESSION['session_user'], ['Hans', 'Fritz'])) {
    exit();
}

date_default_timezone_set("Europe/Berlin");
$time = $_POST['time'];

$user = $_SESSION['session_user'];

$upload_dir = "uploads/$user/";
$extension = "dng";
$target_file = str_replace([':', ' '], ['-', '_'], $upload_dir . $time . "." . $extension);

if (unlink( $target_file)) {
    echo ("Löschung des Bildes erfolgreich!");
} else {
    echo("Fehler beim Löschen des Bildes.");
}

$config = require 'config.php';
$link = mysqli_connect(
    $config['servername'],
    $config['username'],
    $config['password'],
    $config['database']
);

$sql = "DELETE FROM `data_$user` WHERE `time` = '$time';";
if(mysqli_query($link, $sql)){
	echo("Daten erfolgreich gelöscht.");
} 

else{
	echo("Fehler beim löschen der Daten.");
}

header("Location: data_capture.php");

?>