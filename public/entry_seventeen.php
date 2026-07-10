<?php
$titre_page = 'ENTRY NUMBER SEVENTEEN';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titre_page) ?></title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">
</head>
<body class="page-entry">
    <audio id="entryAudio" src="assets/audio/entry_seventeen.mp3" preload="auto" autoplay loop></audio>

    <main class="entry-wrap">
        <section class="entry-box">
            <p class="entry-small">ENTRY NUMBER SEVENTEEN</p>
            <h1>DARK DARKER YET DARKER</h1>
            <p>THE READINGS ARE UNSTABLE</p>
            <p>THE ROOM KEEPS GETTING DARKER</p>
            <p>NEGATIVE LIGHT DETECTED</p>
            <p>THE NEXT TEST FEELS</p>
            <p>VERY</p>
            <p>VERY</p>
            <p>INTERESTING</p>
            <button id="entryStart" type="button">LISTEN</button>
            <a href="index.php">RETURN</a>
        </section>
    </main>

    <script src="assets/js/app.js?v=<?= filemtime(__DIR__ . '/assets/js/app.js') ?>" defer></script>
</body>
</html>
