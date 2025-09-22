<?php
	session_start();
	
	// Check if user is logged in
	if (!isset($_SESSION["userId"]))
	{
		returnWithError("No active session found");
	}
	else
	{
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
		
		returnWithSuccess("Logout successful");
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