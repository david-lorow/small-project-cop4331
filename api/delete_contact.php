<?php
require_once __DIR__ ."/auth.php";

//Get session data
$UserID = getID();

//Get contact data
$inData = getData();
if(empty($inData["ID"])) 
{
    returnWithError("Missing contact information", 400);
}

//Get contact details, if not found func will send error
$contact = getContactInfo($inData["ID"], $UserID);

//If found, delete
if(!deleteContact($contact["ID"], $UserID))
{
    returnWithError("Unsuccessful deletion", 400);
}
sendResultInfoAsJson(json_encode($contact));

function deleteContact($ID, $UserID): bool
{
    $conn = get_conn();
    $stmt = $conn->prepare("DELETE FROM Contacts WHERE ID=? AND UserID=?");
    if(!$stmt) returnWithError($conn->error, 500);
    $stmt->bind_param("ii", $ID, $UserID);
    $stmt->execute();

    return ($stmt->affected_rows !== 0);
}
