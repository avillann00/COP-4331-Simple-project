<?php

    session_start();
    

    $inData = getRequestInfo();
    if (!isset($_SESSION["userId"])) {
        returnWithError("Not logged in");
        exit();
    }

    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];
    $email = $inData["email"];
    $number = $inData["phone"];
    $userID = $_SESSION["userId"];
    #$userID = $indata["userID"];

    $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
    if ($conn->connect_error)
        {
            returnWithError( $conn->connect_error );
        }
        else{
             // First check if contact already exists
                $stmt = $conn->prepare("SELECT ID FROM Contacts WHERE phone=? AND UserID=?");
                $stmt->bind_param("si", $number, $userID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->fetch_assoc())
                {
                        returnWithError("Contact Already Exists");
                }
                 else
                {
                        // Insert new contact
                        $stmt = $conn->prepare("INSERT into Contacts (FirstName,LastName,Email,Phone,UserID) VALUES(?,?,?,?,?)");
                        $stmt->bind_param("ssssi", $firstName, $lastName, $email, $number, $userID);
                        $stmt->execute();

                        if ($stmt->affected_rows > 0)
                        {
                                $newUserId = $conn->insert_id;
                                returnWithInfo($firstName, $lastName, $newUserId);
                        }
                        else
                        {
                                returnWithError("Contact Creation Failed");
                        }
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
