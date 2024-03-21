<?php
    require_once("connect.php");
    $email = $_GET["email"];

    $stmt = $conn->prepare("SELECT * FROM user WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "exist"; 
    } else {
        echo "not_exist"; 
    }
?>