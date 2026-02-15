<?php
ini_set('session.use_strict_mode', 1);//Keeping things secure
session_set_cookie_params([
  'lifetime' => 0, //Whenever the browser closes
  'path' => '/',
  'domain' => '',         
  'secure' => false, //Just in case      
  'httponly' => true,     
  'samesite' => 'Lax',   
]);
session_start();

require_once __DIR__ . "/helpers.php";

$inData = getData();

//My philosophy is to put the unhappy paths in if, and continue otherwise
if(empty($inData["login"]) || empty($inData["Password"])) 
{
    returnWithError("Missing login or password");
}

//Begin DB connection
$conn = get_conn();
$stmt = $conn->prepare("SELECT ID, FirstName, LastName, Password FROM Users WHERE Login=? LIMIT 1");
if(!$stmt) returnWithError($conn->error, 500);
$stmt->bind_param("s", $inData["login"]);//Get only login

//Pull login info
$stmt->execute();
$result = $stmt->get_result();
//In case of no result
if(!($row = $result->fetch_assoc())) 
{
    returnWithError("Invalid login or password", 401);
}

//Check given password against hashed version
if(!password_verify($inData["Password"], $row["Password"])) 
{
    returnWithError("Invalid login or password", 401);
}

//Session creation
session_regenerate_id(true); //Extra refresh
$_SESSION['UserID'] = (int)$row["ID"];
$_SESSION['first_name'] = $row["FirstName"];
$_SESSION['last_name']  = $row["LastName"];
$_SESSION['login']      = $inData["login"];
$_SESSION['logged_in_at'] = time();
session_write_close(); //Writing's over

returnWithInfo($row["FirstName"], $row["LastName"], $row["ID"]);




