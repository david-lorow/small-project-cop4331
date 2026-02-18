<?php
require_once __DIR__ . "/auth.php";

//Get session data
$UserID = getID();

//Get last 10 contact IDs for this user
$contacts = getLastTenContactIDs($UserID);

if(!$contacts["matches?"])
{
    returnWithError("Nobody found", 404);
}

//Finally, send results
sendContactsByIDs($contacts, $UserID);

//Returns up to the last 10 contact IDs (newest first)
function getLastTenContactIDs($UserId)
{
    $conn = get_conn();

    //ID DESC = newest first, LIMIT 10 = up to ten rows
    $stmt = $conn->prepare("SELECT ID FROM Contacts WHERE UserID=? ORDER BY ID DESC LIMIT 10");
    if(!$stmt) returnWithError($conn->error, 500);

    $stmt->bind_param("i", $UserId);
    $stmt->execute();
    $result = $stmt->get_result();

    $ids = [];
    while($row = $result->fetch_assoc())
    {
        $ids[] = (int)$row["ID"];
    }

    return [
        "matches?" => count($ids) > 0,
        "IDs" => $ids
    ];
}

