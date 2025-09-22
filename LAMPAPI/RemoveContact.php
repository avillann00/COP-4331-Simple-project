<?php

    session_start();
    

    $inData = getRequestInfo();
    if (!isset($_SESSION["userId"])) {
        returnWithError("Not logged in");
        exit();
    }

    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];
    $phone = $inData["phone"];
    $email = $inData["email"];
    $userID = $_SESSION["userId"];
    #$userID = $inData["userID"];

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if($conn->connect_error)
    {
        returnWithError($conn->connect_error);
    }
    else
    {
        // First check that user exists
        $stmt = $conn->prepare("SELECT ID FROM Contacts WHERE firstName=? AND lastName=? AND phone=? AND email=? AND UserID=?");
        $stmt->bind_param("ssssi", $firstName, $lastName, $phone, $email, $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->fetch_assoc())
        {
            $stmt = $conn->prepare("DELETE FROM Contacts WHERE ID=?");
            $stmt->bind_param("i", $result);
            $stmt->execute();
        }
        else
        {
            returnWithError("Contact not found");
        }

        $stmt->close();
        $conn->close();
    }

    function sendResultInfoAsJson( $obj )
    {
        header('Content-type: application/json');
        echo $obj;
    }

    function returnWithError( $err )
    {
        $retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }

    function returnWithInfo( $firstName, $lastName, $id )
    {
        $retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '", "lastName":"' . $lastName . '","error":""}';
        sendResultInfoAsJson( $retValue );
    }

    function getRequestInfo()
    {
        return json_decode(file_get_contents('php://input'), true);
    }
?>
