<?php
session_start();

if (!isset($_SESSION['status'])) {
    $_SESSION['status'] = false; 
}

$_SESSION['status'] = !$_SESSION['status']; 

if (!isset($_SESSION['status']) || $_SESSION['status'] !== false) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['url'])) {
    $urlToRemove = $_GET['url'];
    $csvFile = "../data/websites.csv";
    $websites = [];

    if (($handle = fopen($csvFile, "r")) !== false) {
        $headers = fgetcsv($handle, 0, ',', '"', '\\'); 
        while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if ($data[1] !== $urlToRemove) { 
                $websites[] = $data;
            }
        }
        fclose($handle);
    }

    if (($handle = fopen($csvFile, "w")) !== false) {
        fputcsv($handle, $headers); 
        foreach ($websites as $site) {
            fputcsv($handle, $site);
        }
        fclose($handle);
    }
}


header("Location: ../index.php");
exit();
?>
