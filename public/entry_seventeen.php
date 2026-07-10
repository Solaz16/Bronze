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
            <h1>dark darker yet darker</h1>
            <p>the readings are unstable</p>
            <p>the room keeps getting darker</p>
            <p>negative light detected</p>
            <p>the next test feels</p>
            <p>very</p>
            <p>very</p>
            <p>interesting</p>
            <button id="entryStart" type="button">listen</button>
            <a href="index.php">return</a>
        </section>
    </main>

    <script src="assets/js/app.js?v=<?= filemtime(__DIR__ . '/assets/js/app.js') ?>" defer></script>
</body>
</html>
