<?php

        $inData = getRequestInfo();

        $firstName = $inData["firstName"];
        $lastName = $inData["lastName"];
        $login = $inData["login"];
        $password = $inData["password"];

        $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
        if ($conn->connect_error)
        {
                returnWithError( $conn->connect_error );
        }
        else
        {
                // First check if login already exists
                $stmt = $conn->prepare("SELECT ID FROM Users WHERE Login=?");
                $stmt->bind_param("s", $login);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->fetch_assoc())
                {
                        returnWithError("User Already Exists");
                }
                else
                {
                        // Insert new user
                        $stmt = $conn->prepare("INSERT into Users (FirstName,LastName,Login,Password) VALUES(?,?,?,?)");
                        $stmt->bind_param("ssss", $firstName, $lastName, $login, $password);
                        $stmt->execute();

                        if ($stmt->affected_rows > 0)
                        {
                                $newUserId = $conn->insert_id;
                                returnWithInfo($firstName, $lastName, $newUserId);
                        }
                        else
                        {
                                returnWithError("Registration Failed");
                        }
                }

                $stmt->close();
                $conn->close();
        }

        function getRequestInfo()
        {
                return json_decode(file_get_contents('php://input'), true);
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
                $retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
                sendResultInfoAsJson( $retValue );
        }

?>
