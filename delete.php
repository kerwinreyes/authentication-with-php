<?php
include 'dbconnection.php';    
$userId = $_GET['user_id'];
$data = $conn->prepare("DELETE address, city, state, province FROM users_address where user_id = ?");
$data->bind_param("s", $userId);
$data->execute();

header("location: home.php");
?>