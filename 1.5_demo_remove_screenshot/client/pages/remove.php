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
    $headers = [];

    if (($handle = fopen($csvFile, "r")) !== false) {
        $headers = fgetcsv($handle, 0, ',', '"', '\\'); 
        while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            if ($data[1] === $urlToRemove) {
                $imagePath = trim($data[2]); // Get the stored image path
                $basename = pathinfo($imagePath, PATHINFO_BASENAME); // Get only the filename


                if (file_exists($imagePath)) {
                    unlink($imagePath); // Remove local image
                }

                // Send request to Node.js server to remove the file
                $serverApiUrl = "http://localhost:4000/remove?url=" . urlencode($basename);
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $serverApiUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); // Use DELETE
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode !== 200) {
                    error_log("Failed to remove screenshot from server. Response: $response");
                }
            } else {
                $websites[] = $data;
            }
        }
        fclose($handle);
    }

    // Rewrite CSV file without the removed entry
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

