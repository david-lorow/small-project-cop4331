<?php
require_once __DIR__ ."/auth.php";

//Get session data
$UserID = getID();

//Get contact data
$inData = getData();

if(empty($inData["FirstName"]) && empty($inData["LastName"])) 
{
    returnWithError("Missing info", 400);
}
if(empty($inData["FirstName"])) 
{
    $inData["FirstName"] = $inData["LastName"];
}
if(empty($inData["LastName"]))
{
    $inData["LastName"] = $inData["FirstName"];
}

//So here, searching is going to be based on name
$contacts = checkForContact($UserID, $inData["FirstName"], $inData["LastName"]);

if(!$contacts["matches?"]) 
{
    returnWithError("Nobody found", 404);
}

$contactArray = [];

$idArray = $contacts["IDs"];
for($i = 0; $i < count($idArray); $i++)
{
    $contact = getContactInfo($idArray[$i], $UserID);
    $contactArray[] = $contact;
}

//Finally, send search results
sendSearchResults($contactArray);

function sendSearchResults($contactArray) 
{
    http_response_code(200);
    sendResultInfoAsJson(json_encode([
        "results" => $contactArray,
        "error" => ""
    ]));
    exit;//No need to continue
}

function checkForContact($UserId, $FirstName, $LastName)
{
    $conn = get_conn();
    $stmt = $conn->prepare("SELECT ID FROM Contacts WHERE UserID=? AND (FirstName LIKE ? OR LastName LIKE ?)");
    if(!$stmt) returnWithError($conn->error, 500);
    //Insert wild cards
    $first = $FirstName . "%";
    $last = $LastName . "%";
    $stmt->bind_param("iss", $UserId, $first, $last);
    $stmt->execute();
    $result = $stmt->get_result();


    $ids = [];
    while($row = $result->fetch_assoc()) 
    {
        $ids[] = (int)$row["ID"]; //Array of matching contact IDs
    }

    return [
        "matches?" => count($ids) > 0,
        "IDs" => $ids
    ];
}