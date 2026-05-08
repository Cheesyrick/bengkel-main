<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_bengkel_bengawan";
$conn = "";

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check if the connection FAILED
if (!$conn) {
    // If it failed, show the error
    die("Connection failed: " . mysqli_connect_error());
}
?>