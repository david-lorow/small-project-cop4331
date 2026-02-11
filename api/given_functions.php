<?php
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