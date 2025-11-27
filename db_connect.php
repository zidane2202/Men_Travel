<?php
$servername = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');
$port = getenv('DB_PORT');

$conn = new mysqli($servername, $username, $password, $dbname, $port);
// ... suite du code
?>
