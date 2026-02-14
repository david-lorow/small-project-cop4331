<?php
require_once __DIR__ . "/auth.php";

//Get session data
$UserID = getID();

//Get request data
$inData = getData();

if(empty($inData["ID"]))
{
    returnWithError("Missing contact ID", 400);
}

$ContactID = (int)$inData["ID"];

//Get/send contact
sendResultInfoAsJson(json_encode(getContactInfo($inData["ID"], $UserID)));




