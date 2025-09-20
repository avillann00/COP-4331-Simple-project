<?php

    session_start();
    

    $inData = getRequestInfo();
    if (!isset($_SESSION["userId"])) {
        returnWithError("Not logged in");
        exit();
    }

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
        $stmt = $conn->prepare("SELECT ID FROM Users WHERE UserID=?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->fetch_assoc())
        {
            $stmt = $conn->prepare("DELETE FROM Users WHERE ID=?");
            $stmt->bind_param("i", $result);
            $stmt->execute();
        }
        else
        {
            returnWithError("User not found");
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