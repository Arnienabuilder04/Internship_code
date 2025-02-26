<?php
session_start();

if (!isset($_SESSION['status'])) {
    $_SESSION['status'] = false; 
}

// CSV fetch logic
function getWebsitesFromCSV($csvFile) {
    $websites = [];
    if (($handle = fopen($csvFile, "r")) !== false) {
        $headers = fgetcsv($handle, 0, ',', '"', '\\'); 
        
        while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== false) { 
            if (count($data) === count($headers)) {
                $websites[] = [
                    'name' => $data[0],
                    'url' => $data[1],
                    'image' => $data[2]
                ];
            }
        }
        fclose($handle);
    }
    return $websites;
}

$websites = getWebsitesFromCSV('./data/websites.csv');
//Login Logic
$valid_username = "admin";
$valid_password = "Test1234";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['loggedin'] = true;
        header("Location: index.php"); 
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Demo TD Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles/reset.css">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="icon" type="image/x-icon" href="./favicon.ico">

</head>
<body>
    <nav>
        <img class="nav__image" src="./assets/logo.svg" alt="logo">
        <ul class="nav__links">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <?php if ($_SESSION['status'] == false): ?>
                    <li><a class="button button--red" href="./pages/remove.php">Remove a website</a></li>
                <?php else: ?>
                    <li><a class="button button--red" href="./pages/remove.php">Cancel</a></li>
                <?php endif; ?>
                <li><a class="button button--empty" href="./pages/logout.php">Log Out</a></li>
            <?php else: ?>
                <p>Log in to continue</p>
            <?php endif; ?>
        </ul>

    </nav>
    <main>
        

    <?php if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) : ?>
        <section class="input__container">
            <form action="" method="post">
                <label>Username:</label>
                <input type="text" name="username" required>

                <label>Password:</label>
                <input type="password" name="password" required>

                <div class="form_options">
                    <button class="button button--full" type="submit">Log in</button>
                </div>
            </form>
            <div class="message__container">
                <?php if (isset($error)): ?>
                    <p class="message"><?= htmlspecialchars($error) ?></p>
                <?php else: ?>
                    <p class="message"></p>
                <?php endif; ?>
            </div>
        </section>
    <?php else : ?>
        <section class="dashboard">
            <div class="dashboard__container">
                <?php if (isset($_SESSION['status']) && $_SESSION['status'] === true) : ?>
                    <?php foreach ($websites as $site): ?>
                            <div class="dashboard__card--remove">
                                <a class="dashboard__card__container" href="./pages/remove.php?url=<?= urlencode($site['url']); ?>">
                                    <div class="dashboard__card__header">
                                        <h2><?= htmlspecialchars($site['name']); ?></h2>
                                        <div class="icons">
                                            <div class="icon icon--remove">
                                                <img class="icon__image" src="./assets/remove.svg" alt="remove icon">
                                            </div>
                                        </div>
                                    </div>
                                    <img class="dashboard__card__image" src="<?= htmlspecialchars($site['image']); ?>" alt="<?= htmlspecialchars($site['name']); ?>">
                                </a>
                            </div>
                        <?php endforeach; ?>
                <?php else : ?>
                    <?php if (empty($websites)): ?>
                        <div class="dashboard__error_card">
                            <div class="dashboard__card__container">
                                <h2>No websites found</h2>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($websites as $site): ?>
                            <div class="dashboard__card">
                                <a class="dashboard__card__container dashboard__card__container--regular" href="<?= htmlspecialchars($site['url']); ?>" target="_blank">
                                    <div class="dashboard__card__header">
                                        <h2><?= htmlspecialchars($site['name']); ?></h2>
                                        <div class="icons">
                                            <div class="icon icon--redirect">
                                                <img class="icon__image" src="./assets/arrow.svg" alt="arrow icon">
                                            </div>
                                        </div>
                                    </div>
                                    <img class="dashboard__card__image" src="<?= htmlspecialchars($site['image']); ?>" alt="<?= htmlspecialchars($site['name']); ?>">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <a href="./pages/add.php" class="dashboard__card dashboard__card--add">
                        <div class="dashboard__card__container dashboard__card__container--add">
                            <h2>Add a site</h2>
                            <div class="icons">
                                <div class="icon icon--redirect">
                                    <img class="icon__image" src="./assets/plus.svg" alt="add icon">
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

</body>
</html>