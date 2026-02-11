<?php
require_once __DIR__ . "/given_functions.php";
function get_conn(): mysqli
{
    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");

    if ($conn->connect_error) 
    {
        returnWithError($conn->connect_error, 500);
    }

    $conn->set_charset("utf8mb4");//Something good to do apparently
    return $conn;
}