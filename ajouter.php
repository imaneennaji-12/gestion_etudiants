<?php
require_once 'functions.php';
exiger_connexion();

const TAILLE_MAX_PHOTO = 1048576; // 1 Mo
const EXTENSIONS_OK = ['jpg', 'jpeg', 'png'];

$etudiants = lire_etudiants();

// Mode modification si ?id=...
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$modif = $id > 0;
if ($modif && !isset($etudiants[$id])) {
    flash('Étudiant introuvable.', 'erreur');
    header('Location: liste.php');
    exit;
}

$valeurs = $modif
    ? $etudiants[$id]
    : ['nom' => '', 'prenom' => '', 'filiere' => '', 'note' => '', 'photo' => ''];
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = nettoyer($_POST['nom'] ?? '');
    $prenom  = nettoyer($_POST['prenom'] ?? '');
    $filiere = $_POST['filiere'] ?? '';
    $note    = str_replace(',', '.', trim($_POST['note'] ?? ''));

    $valeurs = array_merge($valeurs, ['nom' => $nom, 'prenom' => $prenom, 'filiere' => $filiere, 'note' => $note]);

    // --- Validation ---
    if ($nom === '')    $erreurs[] = 'Le nom est obligatoire.';
    if ($prenom === '') $erreurs[] = 'Le prénom est obligatoire.';
    if (!in_array($filiere, FILIERES, true)) $erreurs[] = 'Filière invalide.';
    if (!is_numeric($note) || $note < 0 || $note > 20) $erreurs[] = 'La note doit être un nombre entre 0 et 20.';

    // Bonus : refuser un doublon nom + prénom
    foreach ($etudiants as $e) {
        if ($e['id'] !== $id && strcasecmp($e['nom'], $nom) === 0 && strcasecmp($e['prenom'], $prenom) === 0) {
            $erreurs[] = 'Cet étudiant existe déjà.';
            break;
        }
    }

    // --- Photo (facultative) ---
    $nouvelle_photo = '';
    $tmp = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $f = $_FILES['photo'];
        if ($f['error'] !== UPLOAD_ERR_OK) {
            $erreurs[] = "Erreur lors de l'envoi de la photo (code {$f['error']}).";
        } else {
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, EXTENSIONS_OK, true)) {
                $erreurs[] = 'Extension non autorisée (jpg, jpeg, png).';
            } elseif ($f['size'] > TAILLE_MAX_PHOTO) {
                $erreurs[] = 'La photo dépasse 1 Mo.';
            } elseif (@getimagesize($f['tmp_name']) === false) {
                $erreurs[] = "Le fichier envoyé n'est pas une image valide.";
            } else {
                $tmp = $f['tmp_name'];
                $nouvelle_photo = uniqid('etu_', true) . '.' . $ext;
            }
        }
    }

    // --- Enregistrement ---
    if (empty($erreurs)) {
        $photo = $valeurs['photo'];
        if ($nouvelle_photo !== '' && move_uploaded_file($tmp, DOSSIER_PHOTOS . $nouvelle_photo)) {
            if ($modif) {
                supprimer_photo($photo); // supprime l'ancienne
            }
            $photo = $nouvelle_photo;
        }

        if (!$modif) {
            $id = $etudiants ? max(array_keys($etudiants)) + 1 : 1;
        }
        $etudiants[$id] = [
            'id' => $id, 'nom' => $nom, 'prenom' => $prenom,
            'filiere' => $filiere, 'note' => (float) $note, 'photo' => $photo,
        ];
        sauver_etudiants($etudiants);
        flash($modif ? 'Étudiant modifié avec succès.' : 'Étudiant ajouté avec succès.');
        header('Location: liste.php');
        exit;
    }
}

$titre = $modif ? "Modifier l'étudiant" : 'Ajouter un étudiant';
include 'header.php';
?>
<form method="post" enctype="multipart/form-data" class="carte petit">
    <h2><?= h($titre) ?></h2>

    <?php if ($erreurs): ?>
        <ul class="erreur">
            <?php foreach ($erreurs as $err): ?><li><?= h($err) ?></li><?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <label>Nom
        <input type="text" name="nom" value="<?= h($valeurs['nom']) ?>">
    </label>
    <label>Prénom
        <input type="text" name="prenom" value="<?= h($valeurs['prenom']) ?>">
    </label>
    <label>Filière
        <select name="filiere">
            <option value="">-- Choisir --</option>
            <?php foreach (FILIERES as $f): ?>
                <option value="<?= h($f) ?>" <?= $valeurs['filiere'] === $f ? 'selected' : '' ?>><?= h($f) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Note (0 à 20)
        <input type="text" name="note" value="<?= h((string) $valeurs['note']) ?>">
    </label>
    <label>Photo (jpg, jpeg, png – 1 Mo max, facultative)
        <input type="file" name="photo" accept=".jpg,.jpeg,.png">
    </label>
    <?php if ($modif && $valeurs['photo'] !== '' && file_exists(DOSSIER_PHOTOS . $valeurs['photo'])): ?>
        <p>Photo actuelle : <img class="photo" src="uploads/<?= h($valeurs['photo']) ?>" alt="Photo actuelle"></p>
    <?php endif; ?>

    <button type="submit"><?= $modif ? 'Mettre à jour' : 'Ajouter' ?></button>
    <a href="liste.php">Annuler</a>
</form>
<?php include 'footer.php'; ?>
