<?php
require_once 'functions.php';
exiger_connexion();

$id = (int) ($_GET['id'] ?? 0);
$etudiants = lire_etudiants();

if (isset($etudiants[$id])) {
    supprimer_photo($etudiants[$id]['photo']);
    unset($etudiants[$id]);
    sauver_etudiants($etudiants);
    flash('Étudiant supprimé.');
} else {
    flash('Étudiant introuvable.', 'erreur');
}

header('Location: liste.php');
exit;
