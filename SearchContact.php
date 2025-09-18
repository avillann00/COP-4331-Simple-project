<?php
/*
  FILE: SearchContact.php
  PURPOSE: Find YOUR contacts whose name/email/phone CONTAINS the text you typed.

  HOW TO CALL THIS FROM YOUR HTML PAGE (JavaScript):
    fetch('/LAMPAPI/SearchContact.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'include',                // sends the PHP session cookie set by Login.php
      body: JSON.stringify({ search: 'jo' }) // <-- this little text is the "JSON body"
    })
    .then(r => r.json())
    .then(data => {
      // SUCCESS: data.results is an array of contacts you can show in the page
      // ERROR:   data.error is a message string (if not empty)
    });

  WHAT YOU SEND (request body):
    { "search": "<some text>" }

  WHAT YOU GET BACK (response):
    Success:
      {
        "results": [
          { "id": 12, "firstName": "John", "lastName": "Jones", "email": "jj@x.com", "phone": "407-555-0130" },
          ...
        ],
        "error": ""
      }
    Error (example):
      { "results": [], "error": "Not logged in" }

  NOTES IN PLAIN ENGLISH:
    - This script ONLY searches YOUR contacts (we use the session user id).
    - It looks for your text anywhere inside FirstName, LastName, Email, or Phone.
    - We sort results by LastName, then FirstName, then ID.
*/

session_start(); // Make PHP load your session so we know which user you are

// 1) Read the tiny JSON message from the request body (example: {"search":"jo"})
$rawBody = file_get_contents('php://input');
$inData  = json_decode($rawBody, true);

// 2) If you aren't logged in, we can't search YOUR contacts
if (!isset($_SESSION["userId"])) {
  respond([ "results" => [], "error" => "Not logged in" ]);
}

// 3) Pull out the search text. If missing or empty, tell the caller.
$search = isset($inData["search"]) ? trim($inData["search"]) : "";
if ($search === "") {
  respond([ "results" => [], "error" => "search is required" ]);
}

// 4) Connect to the database (same values your team used elsewhere)
$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331");
if ($conn->connect_error) {
  respond([ "results" => [], "error" => $conn->connect_error ]);
}

// 5) Make a pattern like "%jo%" which means "contains jo anywhere"
$like = "%" . $search . "%";

// 6) SQL we want to run (read it like English):
//    "Give me ID/First/Last/Email/Phone FROM Contacts that belong to THIS user
//     where ANY of these columns contains my search text."
$sql = "
  SELECT ID, FirstName, LastName, Email, Phone
  FROM Contacts
  WHERE UserID = ?
    AND (FirstName LIKE ? OR LastName LIKE ? OR Email LIKE ? OR Phone LIKE ?)
  ORDER BY LastName, FirstName, ID
";

/*
  IMPORTANT BUT SIMPLE:
  The question marks (?) are blanks we fill in. This is called a "prepared statement".
  You don't need to memorize how it works—think of it as:
    1) Write SQL with ?s
    2) Bind the values (user id, and the 4 copies of $like)
    3) Run it
*/
$stmt = $conn->prepare($sql);
$stmt->bind_param(
  "issss",                 // i = integer, s = string
  $_SESSION["userId"],     // the current logged-in user's ID
  $like, $like, $like, $like
);
$stmt->execute();
$result = $stmt->get_result();

// 7) Turn the database rows into a simple array the frontend can use
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

// 8) Send the final JSON back to the page
respond([ "results" => $rows, "error" => "" ]);

/* ===== super small helper so we don't repeat the JSON lines ===== */
function respond($arr) {
  header('Content-Type: application/json');
  echo json_encode($arr);
  exit;
}
