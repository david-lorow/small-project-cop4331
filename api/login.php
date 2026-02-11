<?php
require_once __DIR__ . "/given_functions.php";
require_once __DIR__ . "/db.php";

$inData = getRequestInfo();


//My philosophy is to put the unhappy paths in if, and continue otherwise
if(!$inData || empty($inData["login"]) || empty($inData["Password"])) 
{
    returnWithError("Missing login or password");
}

//Begin DB connection
$conn = get_conn();
if($conn->connect_error) 
{
    returnWithError($conn->connect_error, 500);//General error
}
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

returnWithInfo($row["FirstName"], $row["LastName"], $row["ID"]);




