<?php
    require_once("connect.php");
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $success = false;         

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);
        $result = $stmt->execute();
        if ($result) {
            $success = true; 
            $message = "Registration successful";
            $response = array(
                'success' => $success,
                'message' => $message
            );
            echo json_encode($response);
        } else {
            $message = "Registration fail";
            $response = array(
                'success' => $success,
                'message' => $message
            );
            echo json_encode($response);
        }
    }
    $stmt->close();
    $conn->close();
?>