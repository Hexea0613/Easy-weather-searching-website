<?php
    require_once "connect.php"; 
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email=$_POST["email"];
        $password=$_POST["password"];

        $stmt = $conn->prepare("SELECT id, username, email, password, notification FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $db_username, $db_email, $db_password, $db_notification);
            $stmt->fetch();
            
            if (password_verify($password, $db_password)) {
                session_start();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $db_username;
                $_SESSION['email'] = $db_email;
                $_SESSION['notification'] = $db_notification;

                $success = true; 
                $message = "Login successful";
                $response = array(
                    'success' => $success,
                    'message' => $message
                );
                echo json_encode($response);
            }
            else {
                $success = false; 
                $message = "Incorrect password.";
                $response = array(
                    'success' => $success,
                    'message' => $message
                );
                echo json_encode($response);
            }
        }
        else {
            $success = false; 
            $message = "Email not found.";
            $response = array(
                'success' => $success,
                'message' => $message
            );
            echo json_encode($response);
        }
        $stmt->close();
    }
    $conn->close();
?>
