<?php
require_once __DIR__ . "/helpers.php"; 

session_start();//No need for user id

//Clear session data
$_SESSION = [];

//Expire the session cookie with matching attributes (including samesite)
if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();

    setcookie(session_name(), '', [
        'expires'  => time() - 67, //Time travel
        'path'     => $p['path'],
        'domain'   => $p['domain'],
        'secure'   => $p['secure'],
        'httponly' => $p['httponly'],
        'samesite' => $p['samesite'] ?? 'Lax',//Safety coalescing
    ]);
}


session_destroy();


sendResultInfoAsJson(json_encode([
    "success" => true,
    "error" => ""
]));
exit;
