<?php
// Elindítjuk a session kezelést, hogy elérjük a meglévő adatokat
session_start();

// Kiürítjük a $_SESSION tömböt (törlődik a user_id, username, email)
$_SESSION = array();

// Ha a munkamenethez cookie is tartozott (alapértelmezett), azt is érvénytelenítjük
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Teljesen megsemmisítjük a session-t a szerveren
session_destroy();

// Biztonságos átirányítás vissza a főoldalra
header("Location: index.php");
exit;
