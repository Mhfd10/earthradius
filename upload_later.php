<?php

session_start();

if (!isset($_SESSION['session_user']) || !in_array($_SESSION['session_user'], ['Hans', 'Fritz'])) {
    exit();
}

date_default_timezone_set("Europe/Berlin");
$time = $_POST['time'];

$user = $_SESSION['session_user'];

$upload_dir = "uploads/$user/";
$extension = pathinfo($_FILES["image_upload"]["name"], PATHINFO_EXTENSION);
$target_file = str_replace([':', ' '], ['-', '_'], $upload_dir . $time . "." . $extension);

if (move_uploaded_file($_FILES["image_upload"]["tmp_name"], $target_file)) {
    echo ("Upload erfolgreich!");
} else {
    echo("Fehler beim Hochladen der Datei.");
}

header("Location: data_capture.php");

?>