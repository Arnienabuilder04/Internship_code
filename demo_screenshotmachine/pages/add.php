<?php
$csvFile = '../data/websites.csv';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $url = $_POST['url'] ?? '';

    if (!empty($name) && !empty($url) && filter_var($url, FILTER_VALIDATE_URL)) {
        $file = fopen($csvFile, 'a');

        if ($file) {
            fputcsv($file, [$name, $url,'assets/placeholder.png'], ",", '"', "\\");
            fclose($file);
            header("Location: ../index.php");
        } else {
            $message = "Error opening data(CSV) file";
        }
    } else {
        $message = "Invalid input. Please enter a valid name and URL.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/reset.css">
    <link rel="stylesheet" href="../styles/style.css">
    <title>Add Website</title>
</head>
<body>
    <section class="input__container">
    <form action="" method="post">
        <label for="name">Website Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="url">Website URL:</label>
        <input type="url" name="url" id="url" required>
        <div class="form_options">
            <a href="../index.php" class="button button--empty">Cancel</a>
            <button class="button button--full" type="submit">Add Website</button>
            <a href="../pages/screenshot.php" class="button button--empty">Take Screenshot</a>
        </div>
     
    </form>

    <?php if (!empty($message)): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    </section>
</body>
</html>
