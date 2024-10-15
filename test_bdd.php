<?php
$mysqli = new mysqli("dat", "root", "root", "mydb");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Perform a simple INSERT
$mysqli->query("INSERT INTO test_table (name) VALUES ('test')");

// Perform a simple SELECT
$result = $mysqli->query("SELECT * FROM test_table");
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row["id"]. " - Name: " . $row["name"]. "<br>";
}

$mysqli->close();
?>