<?php
	session_start();
	$inData = getRequestInfo();
	
	$id = 0;
	$firstName = "";
	$lastName = "";

	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		// Get the user record by login only, then verify password separately
		$stmt = $conn->prepare("SELECT ID,firstName,lastName,Password FROM Users WHERE Login=?");
		$stmt->bind_param("s", $inData["login"]);
		$stmt->execute();
		$result = $stmt->get_result();

		if( $row = $result->fetch_assoc() )
		{
			// Verify the password using password_verify
			if( password_verify($inData["password"], $row["Password"]) )
			{
				$_SESSION["userId"] = $row["ID"];   //save userId in session    
				$_SESSION["username"] = $inData["login"]; // Fixed: use the login from input
				returnWithInfo( $row['firstName'], $row['lastName'], $row['ID'] );
			}
			else
			{
				returnWithError("Invalid Credentials");
			}
		}
		else
		{
			returnWithError("Invalid Credentials");
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
