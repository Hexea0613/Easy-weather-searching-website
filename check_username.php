<?php
    require_once("connect.php");
    $username = $_GET["username"];

    $stmt = $conn->prepare("SELECT * FROM user WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "exist"; 
    } else {
        echo "not_exist"; 
    }
?>