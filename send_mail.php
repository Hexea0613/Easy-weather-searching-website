<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    session_start(); 
    require_once "connect.php"; 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $data = json_decode(file_get_contents("php://input"), true);
        $email = $_SESSION["email"];
        $city = $data["city"];
        $user = $_SESSION["username"];

        $sql = "SELECT weather FROM weather WHERE city = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $city);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $weatherJson = $row['weather'];
            $weatherData = json_decode($weatherJson, true);

            $table = '<table border="1">
            <thead>
                <tr>
                    <th></th>
                    <th>時間</th>
                    <th>城市</th>
                    <th>溫度</th>
                    <th>體感溫度</th>
                    <th>濕度</th>
                    <th>天氣情況</th>
                    <th>風速</th>
                </tr>
            </thead>
            <tbody>';

            $count = 0;
            $lowbound = 1;

            foreach ($weatherData as $item) {

                if ($count >= $lowbound) {
                    $icon = $item['icon'];
                    $date = $item['date'];
                    $name = $item['name'];
                    $temperature = $item['temperature'];
                    $temp_like = $item['temp_like'];
                    $humidity = $item['humidity'];
                    $weather = $item['weather'];
                    $windSpeed = $item['windSpeed'];
            
                    $table .= "<tr>
                        <td><img src='https://openweathermap.org/img/wn/{$icon}@2x.png' alt='{$weather}'></td>
                        <td>{$date}</td>
                        <td>{$name}</td>
                        <td>{$temperature}°C</td>
                        <td>{$temp_like}°C</td>
                        <td>{$humidity}%</td>
                        <td>{$weather}</td>
                        <td>{$windSpeed} mph</td>
                    </tr>";
                }
                $count++;
            }

            $table .= '</tbody></table>';

            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host  	= 'wwwdmplus.csie.io';
            $mail->SMTPAuth	= false;
            $mail->SMTPAutoTLS 	= false;
            $mail->Port	= 25;

            $mail->setFrom('dmplus@wwwdmplus.csie.io', 'Weather Forcast');
            $mail->addAddress($email, $user);

            $mail->isHTML(true); //Set email format to HTML
            $mail->Subject	= 'Weather forcast for next five days';
            $mail->Body	= $table;

            $mail->send();
            $response = array(
                'success' => true
            );
            echo json_encode($response);
        }
    }
?>
