<?php

session_start();

if (!isset($_SESSION['session_user']) || !in_array($_SESSION['session_user'], ['Hans', 'Fritz'])) {
    exit();
}

function zipFolder($folderPath, $zipFilePath) {
    $zip = new ZipArchive();
    if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
        $folderPathReal = realpath($folderPath);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folderPathReal, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($folderPathReal) + 1);

                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();
        return true;
    } else {
        return false;
    }
}


function download($filepath) {
    if (file_exists($filepath)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($filepath));
      flush(); // Flush system output buffer
	  readfile($filepath);
	  exit();
	}
	else{
		http_response_code(404);
		exit();
	}
}

$folderPath = 'test';
$zipFilePath = 'test.zip';

if (zipFolder($folderPath, $zipFilePath)) {
    download($zipFilePath);
    unlink($zipFilePath);
} else {
    echo "Failed to create zip file.";
}

header("Location: data_capture.php");
?>