<?php
        session_start();
        $inData = getRequestInfo();

        // Check if user is logged in
        if (!isset($_SESSION["userId"]))
        {
                returnWithError("No active session found");
                exit;
        }

        $userId = $_SESSION["userId"];
        $password = $inData["password"]; // Require password confirmation for security

        $conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");

        if ($conn->connect_error)
        {
                returnWithError($conn->connect_error);
        }
        else
        {
                // First verify the user's password before deleting account
                $stmt = $conn->prepare("SELECT Password FROM Users WHERE ID=?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc())
                {
                        // Verify password
                        if (password_verify($password, $row["Password"]))
                        {
                                // Delete user's contacts first (to avoid foreign key constraints)
                                $deleteContactsStmt = $conn->prepare("DELETE FROM Contacts WHERE UserID=?");
                                $deleteContactsStmt->bind_param("i", $userId);
                                $deleteContactsStmt->execute();
                                $deleteContactsStmt->close();

                                // Delete the user account
                                $deleteStmt = $conn->prepare("DELETE FROM Users WHERE ID=?");
                                $deleteStmt->bind_param("i", $userId);
                                $deleteStmt->execute();

                                if ($deleteStmt->affected_rows > 0)
                                {
                                        // Account deleted successfully, now logout
                                        // Clear all session variables
                                        $_SESSION = array();

                                        // Destroy the session cookie
                                        if (ini_get("session.use_cookies")) {
                                                $params = session_get_cookie_params();
                                                setcookie(session_name(), '', time() - 42000,
                                                        $params["path"], $params["domain"],
                                                        $params["secure"], $params["httponly"]
                                                );
                                        }

                                        // Destroy the session
                                        session_destroy();

                                        returnWithSuccess("Account deleted and logged out successfully");
                                }
                                else
                                {
                                        returnWithError("Failed to delete account");
                                }

                                $deleteStmt->close();
                        }
                        else
                        {
                                returnWithError("Invalid password");
                        }
                }
                else
                {
                        returnWithError("User not found");
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
                $retValue = '{"error":"' . $err . '"}';
                sendResultInfoAsJson( $retValue );
        }

        function returnWithSuccess( $msg )
        {
                $retValue = '{"message":"' . $msg . '","error":""}';
                sendResultInfoAsJson( $retValue );
        }

?>
