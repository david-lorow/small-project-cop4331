<?php
require_once __DIR__ ."/auth.php";

//Get session data
$UserID = getID();

//Get contact data
$inData = getData();
if(!$inData || empty($inData["FirstName"]) || empty($inData["LastName"]) || empty($inData["Phone"]) ||empty($inData["Email"])) 
{
    returnWithError("Missing contact information", 400);
}

//Prepare database
$conn = get_conn();

//Create contact
$stmt = $conn->prepare("INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID) VALUES (?, ?, ?, ?, ?)");
if(!$stmt) returnWithError($conn->error, 500);
$stmt->bind_param("ssssi", $inData["FirstName"], $inData["LastName"], $inData["Phone"], $inData["Email"], $UserID);
if(!$stmt->execute()) 
{
    returnWithError($stmt->error,500);
}

//Send info to frontend
$newId = $conn->insert_id;
returnWithInfo($inData["FirstName"], $inData["LastName"], $newId, $inData["Phone"], $inData["Email"]);

