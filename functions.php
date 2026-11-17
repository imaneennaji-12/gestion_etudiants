<?php
// functions.php : constantes, session et fonctions communes
session_start();

const FICHIER = __DIR__ . '/data/etudiants.txt';
const DOSSIER_PHOTOS = __DIR__ . '/uploads/';
const FILIERES = ['GI', 'IDIA', 'GE', 'GC'];
const UTILISATEURS = [
    'admin' => 'admin123',
    'imane' => 'ensa2026',
];

/** Protège l'affichage contre les failles XSS. */
function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function connecte(): bool
{
    return isset($_SESSION['user']);
}

function exiger_connexion(): void
{
    if (!connecte()) {
        header('Location: index.php');
        exit;
    }
}

/** Supprime espaces inutiles, ";" et retours à la ligne saisis par l'utilisateur. */
function nettoyer(string $s): string
{
    $s = str_replace([';', "\r", "\n"], ['', ' ', ' '], $s);
    return trim(preg_replace('/\s+/', ' ', $s));
}

/** Lit le fichier texte et retourne un tableau associatif indexé par l'id. */
function lire_etudiants(): array
{
    $liste = [];
    if (!file_exists(FICHIER)) {
        return $liste;
    }
    $lignes = file(FICHIER, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lignes as $ligne) {
        $p = explode(';', $ligne);
        if (count($p) < 5) {
            continue; // ligne invalide
        }
        $id = (int) $p[0];
        $liste[$id] = [
            'id'      => $id,
            'nom'     => $p[1],
            'prenom'  => $p[2],
            'filiere' => $p[3],
            'note'    => (float) $p[4],
            'photo'   => $p[5] ?? '',
        ];
    }
    return $liste;
}

/** Réécrit tout le fichier à partir du tableau. */
function sauver_etudiants(array $liste): void
{
    $lignes = [];
    foreach ($liste as $e) {
        $lignes[] = implode(';', [$e['id'], $e['nom'], $e['prenom'], $e['filiere'], $e['note'], $e['photo']]);
    }
    file_put_contents(FICHIER, $lignes ? implode(PHP_EOL, $lignes) . PHP_EOL : '', LOCK_EX);
}

/** Message flash : stocké en session, affiché une seule fois. */
function flash(string $message, string $type = 'succes'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function supprimer_photo(string $nom): void
{
    if ($nom !== '' && file_exists(DOSSIER_PHOTOS . $nom)) {
        unlink(DOSSIER_PHOTOS . $nom);
    }
}
