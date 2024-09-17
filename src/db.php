<?php

$servername = "bynukpngo6colk0paktz-mysql.services.clever-cloud.com";
$username = "usfm6p4dwayzvqag";
$password = "tBtqpyGNpuvqKiW0Dx1w";
$dbname = "bynukpngo6colk0paktz";
$port = "3306";

$conn = new mysqli(
    $servername, 
    $username, 
    $password, 
    $dbname, 
    $port
);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}