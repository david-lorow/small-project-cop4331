
<?php

$inData = getRequestInfo();
if(!$inData || empty($inData["FirstName"]) || empty($inData["LastName"]) || empty($inData["email"]) || empty($inData["Password"])) 
{
    returnWithError("Missing registration information", 400);
}

$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
if($conn->connect_error) 
{
    returnWithError($conn->connect_error, 500);//General error
}

$login = $inData["email"];//Email from registration becomes login username

//Hash the password before storage with PHP's default encryption algo
$hashedPassword = password_hash($inData["Password"], PASSWORD_BCRYPT);
if($hashedPassword === false) 
{
    returnWithError("Failed to hash password", 500);
}

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


//Given functions
function getRequestInfo() 
{
    return json_decode(file_get_contents("php://input"), true);
}

function sendResultInfoAsJson($obj) 
{
    header("Content-type: application/json");
    echo $obj;
}

function returnWithError($err, $code = 400) 
{//Bad request as default
    http_response_code($code);
	sendResultInfoAsJson(json_encode([
        "id" => 0,
        "FirstName" => "",
        "LastName" => "",
        "error" => $err
    ]));
    exit;//No need to continue
}

function returnWithInfo($FirstName, $LastName, $id) {
	http_response_code(200);
    sendResultInfoAsJson(json_encode([
        "id" => (int)$id,
        "FirstName" => $FirstName,
        "LastName" => $LastName,
        "error" => ""
    ]));
    exit;//No need to continue
}
	

