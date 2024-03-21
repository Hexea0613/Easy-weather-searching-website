<?php
    session_start(); 
    require_once "connect.php"; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        $notification = $data["notification"];
        $username = $_SESSION["username"];

        $stmt = $conn->prepare("UPDATE user SET notification=? WHERE username=?");
        $stmt->bind_param("is", $notification, $username);
        $result = $stmt->execute();
        
        $stmt->close();
        $conn->close();
    }

?>