<?php
require_once 'functions.php';

if (connecte()) {
    header('Location: liste.php');
    exit;
}

$erreur = '';
$login = $_COOKIE['dernier_user'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $mdp   = $_POST['mdp'] ?? '';

    if (isset(UTILISATEURS[$login]) && hash_equals(UTILISATEURS[$login], $mdp)) {
        $_SESSION['user'] = $login;
        setcookie('dernier_user', $login, time() + 7 * 24 * 3600); // 7 jours
        header('Location: liste.php');
        exit;
    }
    $erreur = 'Login ou mot de passe incorrect.';
}

$titre = 'Connexion';
include 'header.php';
?>
<form method="post" class="carte petit">
    <h2>Connexion</h2>
    <?php if ($erreur): ?><p class="erreur"><?= h($erreur) ?></p><?php endif; ?>
    <label>Login
        <input type="text" name="login" value="<?= h($login) ?>" required>
    </label>
    <label>Mot de passe
        <input type="password" name="mdp" required>
    </label>
    <button type="submit">Se connecter</button>
</form>
<?php include 'footer.php'; ?>
