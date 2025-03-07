<?php

$dsn = 'mysql:host=localhost;dbname=marketplace';
$username = 'root';
$password = 'ajsuman';

try {
    $db = new PDO($dsn, $username, $password);
    // echo "Connected to the database";
} catch (PDOException $e) {
    $error_message = $e->getMessage();
    include('errors/database_error.php');
    exit();
}

?>