
<?php
require_once __DIR__ . "/helpers.php";

$inData = getData();
if(empty($inData["FirstName"]) || empty($inData["LastName"]) || empty($inData["email"]) || empty($inData["Password"])) 
{
    returnWithError("Missing registration information", 400);
}

//Prepare data
$conn = get_conn();
$login = $inData["email"];//Email from registration becomes login username

//Hash the password before storage with PHP's default encryption algo
$hashedPassword = password_hash($inData["Password"], PASSWORD_BCRYPT);

//Test for existing user
$stmt = $conn->prepare("SELECT ID, FirstName, LastName FROM Users WHERE Login=?");
if(!$stmt) returnWithError($conn->error, 500);
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();
if($result->fetch_assoc()) 
{
    returnWithError("Login already exists", 409);
}

//Insert new user
$stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)");
if(!$stmt) returnWithError($conn->error, 500);
$stmt->bind_param("ssss", $inData["FirstName"], $inData["LastName"], $login, $hashedPassword);
if(!$stmt->execute()) 
{
    returnWithError($stmt->error,500);
}

$newId = $conn->insert_id;
returnWithInfo($inData["FirstName"], $inData["LastName"], $newId);


