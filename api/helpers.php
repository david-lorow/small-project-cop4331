<?php
//Data senders
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
        "Phone" => "",
        "Email" => "",
        "error" => $err
    ]));
    exit;//No need to continue
}

function returnWithInfo($FirstName, $LastName, $id, $Phone = "", $Email = "") 
{
	http_response_code(200);
    sendResultInfoAsJson(json_encode([
        "id" => (int)$id,
        "FirstName" => $FirstName,
        "LastName" => $LastName,
        "Phone" => $Phone,
        "Email" => $Email,
        "error" => ""
    ]));
    exit;//No need to continue
}


//Database
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


//Data getter
function getData()
{
    //Check if it's empty
    $raw = file_get_contents("php://input");
    if ($raw === false || trim($raw) === "") 
    {
        returnWithError("No input data", 400);
    }

    $data = json_decode($raw, true);
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) 
    {
        returnWithError("Invalid JSON", 400);
    }
    if (!is_array($data)) 
    {
        returnWithError("JSON must be an object", 400);
    }
    return $data;
}

function getContactInfo($ID, $UserID)
{
    $conn = get_conn();
    $stmt = $conn->prepare("SELECT ID, FirstName, LastName, Phone, Email FROM Contacts WHERE ID=? AND UserID=?");
    if(!$stmt) returnWithError($conn->error, 500);
    $stmt->bind_param("ii", $ID, $UserID);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();
    if(!$row) returnWithError("Contact not found", 404);

    return $row;
}
