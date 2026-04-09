<?php

session_start();

error_reporting(E_ALL);

$config = require 'config.php';
$link = mysqli_connect(
    $config['servername'],
    $config['username'],
    $config['password'],
    $config['database']
);

$input = htmlentities($_POST['name']);
$user = mysqli_real_escape_string($link, $input);
$password = mysqli_real_escape_string($link, $_POST['password']);

$sql = "SELECT * FROM `users` WHERE username = '$user'";
$result = mysqli_query($link, $sql);
$resultCheck = mysqli_num_rows($result);

if ($row = mysqli_fetch_assoc($result)) {
	$hashedPassword = password_verify($password, $row['password']);
	
    if ($hashedPassword == false) {
        exit("Passwort falsch");
    }
	
	elseif($hashedPassword == true){
		$_SESSION['session_user'] = $user;
		header("Location: data_capture.php");
		exit();
    }
}

?>