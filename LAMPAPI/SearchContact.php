<?php

session_start(); // Make PHP load your session so we know which user you are

// 1) Read the tiny JSON message from the request body (example: {"search":"jo"})
$rawBody = file_get_contents('php://input');
$inData  = json_decode($rawBody, true);

// 2) If you aren't logged in, we can't search YOUR contacts
if (!isset($_SESSION["userId"])) {
  respond([ "results" => [], "error" => "Not logged in" ]);
}

// 3) Pull out the search text. If missing, set to empty string.
$search = isset($inData["search"]) ? trim($inData["search"]) : "";

// 4) Connect to the database (same values your team used elsewhere)
$conn = new mysqli("db", "TheBeast", "WeLoveCOP4331", "COP4331");
if ($conn->connect_error) {
  respond([ "results" => [], "error" => $conn->connect_error ]);
}

// 5) Build SQL query based on whether search is empty or not
if ($search === "") {
  // Return ALL contacts for this user when search is empty
  $sql = "
    SELECT ID, FirstName, LastName, Email, Phone
    FROM Contacts
    WHERE UserID = ?
    ORDER BY LastName, FirstName, ID
    LIMIT 15
  ";
  
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $_SESSION["userId"]);
} else {
  // Search for contacts containing the search text
  $like = "%" . $search . "%";
  
  $sql = "
    SELECT ID, FirstName, LastName, Email, Phone
    FROM Contacts
    WHERE UserID = ?
      AND (FirstName LIKE ? OR LastName LIKE ? OR Email LIKE ? OR Phone LIKE ?)
    ORDER BY LastName, FirstName, ID
    LIMIT 15
  ";
  
  $stmt = $conn->prepare($sql);
  $stmt->bind_param(
    "issss",                 // i = integer, s = string
    $_SESSION["userId"],     // the current logged-in user's ID
    $like, $like, $like, $like
  );
}

$stmt->execute();
$result = $stmt->get_result();

// 6) Turn the database rows into a simple array the frontend can use
$rows = [];
while ($r = $result->fetch_assoc()) {
  $rows[] = [
    "id"        => (int)$r["ID"],
    "firstName" => $r["FirstName"],
    "lastName"  => $r["LastName"],
    "email"     => $r["Email"],
    "phone"     => $r["Phone"]
  ];
}

$stmt->close();
$conn->close();

// 7) Send the final JSON back to the page
respond([ "results" => $rows, "error" => "" ]);

/* ===== super small helper so we don't repeat the JSON lines ===== */
function respond($arr) {
  header('Content-Type: application/json');
  echo json_encode($arr);
  exit;
}
?>
