<?php
// Démarrer la session
session_start();

// Supprimer toutes les variables de session
$_SESSION = array();

// Si c'est souhaité, détruire la session complètement
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalement, détruire la session.
session_destroy();

// Rediriger vers la page de connexion

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Une date dans le passé
header('Location: logging.html');
exit();
?>
