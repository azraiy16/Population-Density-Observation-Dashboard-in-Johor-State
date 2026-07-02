<?php
$host = "localhost";
$user = "root";
$pass = "";                  // default XAMPP = no password
$db   = "johor_population";  // this must match the DB name in your SQL file

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>