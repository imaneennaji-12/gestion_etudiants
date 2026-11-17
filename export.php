<?php
// Bonus : export CSV de la liste
require_once 'functions.php';
exiger_connexion();

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="etudiants.csv"');

$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // BOM pour Excel
fputcsv($out, ['id', 'nom', 'prenom', 'filiere', 'note'], ';');
foreach (lire_etudiants() as $e) {
    fputcsv($out, [$e['id'], $e['nom'], $e['prenom'], $e['filiere'], $e['note']], ';');
}
fclose($out);
