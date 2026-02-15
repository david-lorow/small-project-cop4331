<?php
require_once __DIR__ . "/helpers.php";
function getID(): int
{
    //Get a session started
    if (session_status() === PHP_SESSION_NONE) 
    {
        session_start();
    }

    //No logon
    if (empty($_SESSION['UserID'])) 
    {
        returnWithError("Not logged in", 401);
    }

    return (int)$_SESSION['UserID'];
}
