<?php
session_start();

if (!isset($_SESSION['session_user']) || !in_array($_SESSION['session_user'], ['Hans', 'Fritz'])) {
    exit();
}

// DB-Zugangsdaten
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "earthradius";
$tables = ["data_Fritz", "data_Hans"];

// Verbindung zur Datenbank
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Datei vorbereiten
$filename = "export_{$dbname}.txt";
$file = fopen($filename, "w");

// Kopfzeile
fwrite($file, "# Datenbank: {$dbname}\n\n");

// Tabellenstruktur nur einmal schreiben (von der ersten Tabelle)
$descResult = $conn->query("DESCRIBE `{$tables[0]}`");
fwrite($file, "# Tabellenstruktur \n");
fwrite($file, "# Spalte,Typ,Null,Standard\n");

while ($row = $descResult->fetch_assoc()) {
    $field = $row['Field'];
    $type = $row['Type'];
    $null = $row['Null'];
    $default = $row['Default'] ?? 'NULL';
    fwrite($file, "# {$field},{$type},{$null},{$default}\n");
}
fwrite($file, "\n");

// Daten aus beiden Tabellen schreiben
foreach ($tables as $table) {
    fwrite($file, "# Daten: {$table}\n");

    $dataResult = $conn->query("SELECT * FROM `$table`");

    while ($row = $dataResult->fetch_assoc()) {
        $escaped = array_map(function($val) {
            return str_replace(",", ".", $val); // falls Kommas als Dezimaltrenner
        }, $row);
        fwrite($file, implode(",", $escaped) . "\n");
    }

    fwrite($file, "\n");

fclose($file);
$conn->close();

// force download
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);

// delete file
unlink($filename);
exit;

header("Location: data_capture.php");
?>
