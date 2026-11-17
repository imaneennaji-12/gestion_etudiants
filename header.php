<?php $titre = $titre ?? 'Gestion des étudiants'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($titre) ?> – Gestion des étudiants</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
<header class="entete">
    <h1>🎓 Gestion des étudiants</h1>
    <?php if (connecte()): ?>
        <nav>
            <a href="liste.php">Liste</a>
            <a href="ajouter.php">Ajouter</a>
            <a href="export.php">Export CSV</a>
            <a href="logout.php">Déconnexion (<?= h($_SESSION['user']) ?>)</a>
        </nav>
    <?php endif; ?>
</header>
<main class="contenu">
