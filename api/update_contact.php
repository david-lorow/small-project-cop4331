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

//If found, udpate
$updatedContact = updateContact($contact, $inData, $inData["ID"], $UserID);
sendResultInfoAsJson((json_encode($updatedContact)));

function updateContact($currentContact, $newData, $ID, $UserID)
{
    //Merge and overwrite only if new value is set AND not null AND not empty string
    $fields = ["FirstName", "LastName", "Phone", "Email"];
    $merged = $currentContact;
    foreach($fields as $f) 
    {
        if(array_key_exists($f, $newData) && ($newData[$f] !== null) && ($newData[$f] !== "")) 
        {
            $merged[$f] = $newData[$f];
        }
    }

    //Update DB
    $conn = get_conn();
    if ($conn->connect_error) returnWithError($conn->connect_error, 500);
    $stmt = $conn->prepare("UPDATE Contacts SET FirstName = ?, LastName = ?, Phone = ?, Email = ? WHERE ID = ? AND UserID = ?");
    if (!$stmt) returnWithError($conn->error, 500);
    $stmt->bind_param("ssssii", $merged["FirstName"], $merged["LastName"], $merged["Phone"], $merged["Email"], $ID, $UserID);
    if (!$stmt->execute()) returnWithError($stmt->error, 500);

    //Return merged result 
    $merged["ID"] = (int)$ID;
    return $merged;
}
