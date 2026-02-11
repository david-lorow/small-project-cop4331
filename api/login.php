<?php
$inData = getRequestInfo();


//My philosophy is to put the unhappy paths in if, and continue otherwise
if(!$inData || empty($inData["login"]) || empty($inData["Password"])) 
{
    returnWithError("Missing login or password");
}

//Begin DB connection
$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
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
    //$retValue = '{"id":0,"FirstName":"","LastName":"","error":"' . $err . '"}'; names with quotes can break it
	sendResultInfoAsJson(json_encode([
        "id" => 0,
        "FirstName" => "",
        "LastName" => "",
        "error" => $err
    ]));
    exit;//No need to continue
}

function returnWithInfo($FirstName, $LastName, $id) 
{
	http_response_code(200);
    sendResultInfoAsJson(json_encode([
        "id" => (int)$id,
        "FirstName" => $FirstName,
        "LastName" => $LastName,
        "error" => ""
    ]));
    exit;//No need to continue
}

