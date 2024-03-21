<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $cityName =$_POST["city"];
        $apiKey = "7d2ae9f0deef8deb158eeebea469dd8b";
        $geocodingUrl = "http://api.openweathermap.org/geo/1.0/direct?q=$cityName&limit=1&appid=$apiKey";
        $geocodingData = json_decode(file_get_contents($geocodingUrl), true);
        if (count($geocodingData) == 0) {
            $success = false; 
            $message = "City name not found!";
            $response = array(
                'success' => $success,
                'message' => $message
            );
            echo json_encode($response);
        } 
        else {
            $name = $geocodingData[0]['name'];
            $lat = $geocodingData[0]['lat'];
            $lon = $geocodingData[0]['lon'];
            $currentWeatherUrl = "https://api.openweathermap.org/data/2.5/weather?lat=$lat&lon=$lon&appid=$apiKey&units=metric&lang=zh_tw";
            $forecastUrl = "https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&appid=$apiKey&units=metric&lang=zh_tw";
            
            $forecastJson = file_get_contents($forecastUrl);
            $forecastData = json_decode($forecastJson, true);

            $currentWeatherJson = file_get_contents($currentWeatherUrl);
            $currentWeatherData = json_decode($currentWeatherJson, true);

            if ($forecastData['cod'] == 200 && $currentWeatherData['cod'] == 200){
                $todayWeather = [
                    'name' => $name,
                    'weather' => $currentWeatherData['weather'][0]['description'],
                    'temperature' => $currentWeatherData['main']['temp'],
                    'humidity' => $currentWeatherData['main']['humidity'],
                    'windSpeed' => $currentWeatherData['wind']['speed']
                ];

                $fiveDayWeather = [];
                $previousDate = '';
                foreach ($forecastData['list'] as $item) {
                    $itemDate = date('Y-m-d', $item['dt']);
            
                    if ($itemDate !== $previousDate) {
                        $fiveDayWeather[] = [
                            'icon' => $item['weather'][0]['icon'],
                            'date' => $itemDate,
                            'name' => $name,
                            'temperature' => $item['main']['temp'],
                            'temp_like' => $item['main']['feels_like'],
                            'humidity' => $item['main']['humidity'],
                            'weather' => $item['weather'][0]['description'],
                            'windSpeed' => $item['wind']['speed']
                        ];
                        $previousDate = $itemDate;
                    }
                }

                $response = [
                    'success' => true,
                    'todayWeather' => $todayWeather,
                    'fiveDayWeather' => $fiveDayWeather
                ];
            }

            header('Content-Type: application/json');
            echo json_encode($response);

            require_once("connect.php");

            $city = $name;
            $weatherJson = json_encode($fiveDayWeather);
            $startDate = $fiveDayWeather[0]['date'];

            $sql = "SELECT * FROM weather WHERE city = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $city);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                $sql = "INSERT INTO weather (city, weather, startdate) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sss", $city, $weatherJson, $startDate);
                $result = $stmt->execute();
            }
            else{
                $row = $result->fetch_assoc();
                if ($row['startdate'] != $startDate) {
                    $sql = "UPDATE weather SET weather = ?, startdate = ? WHERE city = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sss", $weatherJson, $startDate, $city);
                    $result = $stmt->execute();
                }
            }

            $stmt->close();
            $conn->close();
        }
        
    }    
       
        
?>
