<?php
require_once 'functions.php';
exiger_connexion();

$etudiants = lire_etudiants();

// --- Recherche ---
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $etudiants = array_filter($etudiants, function ($e) use ($q) {
        return stripos($e['nom'], $q) !== false || stripos($e['prenom'], $q) !== false;
    });
}

// --- Tri ---
$tri = $_GET['tri'] ?? 'ajout';
$tris_valides = ['ajout' => "Ordre d'ajout", 'nom' => 'Nom', 'filiere' => 'Filière', 'note' => 'Note décroissante'];
if (!array_key_exists($tri, $tris_valides)) {
    $tri = 'ajout';
}
switch ($tri) {
    case 'nom':
        usort($etudiants, fn($a, $b) => strcasecmp($a['nom'], $b['nom']));
        break;
    case 'filiere':
        usort($etudiants, fn($a, $b) => strcasecmp($a['filiere'], $b['filiere']));
        break;
    case 'note':
        usort($etudiants, fn($a, $b) => $b['note'] <=> $a['note']);
        break;
    default:
        usort($etudiants, fn($a, $b) => $a['id'] <=> $b['id']);
}

// --- Statistiques ---
$nb = count($etudiants);
$notes = array_column($etudiants, 'note');
$moyenne = $nb > 0 ? number_format(array_sum($notes) / $nb, 2) : '–';
$meilleure = $nb > 0 ? max($notes) : '–';

// Bonus : moyenne par filière
$par_filiere = [];
foreach ($etudiants as $e) {
    $par_filiere[$e['filiere']][] = $e['note'];
}

// --- Message flash ---
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$titre = 'Liste des étudiants';
include 'header.php';
?>
<?php if ($flash): ?>
    <p class="<?= h($flash['type']) ?>"><?= h($flash['message']) ?></p>
<?php endif; ?>

<form method="get" class="carte filtres">
    <input type="text" id="q" name="q" placeholder="Rechercher un nom ou prénom…" value="<?= h($q) ?>">
    <select name="tri">
        <?php foreach ($tris_valides as $cle => $libelle): ?>
            <option value="<?= h($cle) ?>" <?= $tri === $cle ? 'selected' : '' ?>><?= h($libelle) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Appliquer</button>
</form>

<p class="stats">
    <strong><?= $nb ?></strong> étudiant(s) affiché(s) ·
    Moyenne : <strong><?= $moyenne ?></strong> ·
    Meilleure note : <strong><?= $meilleure ?></strong>
    <?php if ($par_filiere): ?>
        <br><small>Par filière :
        <?php foreach ($par_filiere as $f => $ns): ?>
            <?= h($f) ?> = <?= number_format(array_sum($ns) / count($ns), 2) ?>&nbsp;&nbsp;
        <?php endforeach; ?></small>
    <?php endif; ?>
</p>

<table>
    <thead>
        <tr><th>Photo</th><th>Nom</th><th>Prénom</th><th>Filière</th><th>Note</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php if (empty($etudiants)): ?>
        <tr><td colspan="6">Aucun étudiant</td></tr>
    <?php else: ?>
        <?php foreach ($etudiants as $e): ?>
            <tr>
                <td>
                    <?php if ($e['photo'] !== '' && file_exists(DOSSIER_PHOTOS . $e['photo'])): ?>
                        <img class="photo" src="uploads/<?= h($e['photo']) ?>" alt="Photo de <?= h($e['prenom']) ?>">
                    <?php else: ?>
                        <span class="photo vide">?</span>
                    <?php endif; ?>
                </td>
                <td><?= h(strtoupper($e['nom'])) ?></td>
                <td><?= h(ucfirst($e['prenom'])) ?></td>
                <td><?= h($e['filiere']) ?></td>
                <td class="<?= $e['note'] >= 10 ? 'note-ok' : 'note-ko' ?>"><?= h((string) $e['note']) ?></td>
                <td>
                    <a href="ajouter.php?id=<?= $e['id'] ?>">Modifier</a> |
                    <a class="supprimer" href="supprimer.php?id=<?= $e['id'] ?>">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
<?php include 'footer.php'; ?>
