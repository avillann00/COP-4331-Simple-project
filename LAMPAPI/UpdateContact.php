<?php
session_start();

$inData = getRequestInfo();
if (!isset($_SESSION["userId"])) {
    returnWithError("Not logged in");
    exit();
}

// Get data from JSON
$firstName = $inData["firstName"];
$lastName = $inData["lastName"];
$email = $inData["email"];
$number = $inData["phone"]; // use as unique identifier
$userID = $_SESSION["userId"];

$conn = new mysqli("db", "TheBeast", "WeLoveCOP4331", "COP4331");
if ($conn->connect_error) {
    returnWithError($conn->connect_error);
    exit();
}

// Check if contact exists for this user by phone
$stmt = $conn->prepare("SELECT ID FROM Contacts WHERE Phone=? AND UserID=?");
$stmt->bind_param("si", $number, $userID);
$stmt->execute();
$result = $stmt->get_result();

if (!$row = $result->fetch_assoc()) {
    returnWithError("Contact not found");
    $stmt->close();
    $conn->close();
    exit();
}

// Update the contact using that row
$contactId = $row["ID"];
$stmt = $conn->prepare("UPDATE Contacts SET FirstName=?, LastName=?, Email=? WHERE ID=? AND UserID=?");
$stmt->bind_param("sssii", $firstName, $lastName, $email, $contactId, $userID);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    returnWithInfo($firstName, $lastName, $contactId);
} else {
    returnWithError("Update Failed or No Changes Made");
}

$stmt->close();
$conn->close();

// ---------------- JSON helper functions ----------------
function sendResultInfoAsJson($obj) {
    header('Content-type: application/json');
    echo $obj;
}

function returnWithError($err) {
    $retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
    sendResultInfoAsJson($retValue);
}

function returnWithInfo($firstName, $lastName, $id) {
    $retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
    sendResultInfoAsJson($retValue);
}

function getRequestInfo() {
    return json_decode(file_get_contents('php://input'), true);
}
?>
