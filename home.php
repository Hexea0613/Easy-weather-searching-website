<?php
    session_start();  
    $isLoggedIn = isset($_SESSION["username"]);
    if(isset($_SESSION["username"]))
    {
        $username = $_SESSION["username"];
    }
    
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="sweetalert2.all.min.js"></script>
    <title>What Weather</title>

    <script>
        function validateForm() {
            var city = document.getElementById("city").value;
            var sql_pattern = /['\"]/;
            var english_pattern = /^[A-Za-z]+$/;

            if (!english_pattern.test(city)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Search errors!',
                    text: 'Please enter English name!',
                })
                document.getElementById("searchForm").reset();
                return false;
            }
                
            if(sql_pattern.test(city)){
                Swal.fire({
                    icon: 'error',
                    title: 'Search errors!',
                    text: 'The problem of SQL injection occurs.',
                })
                return false;
            }
            else{
                return true;
            }
        }

        function sendEmail()
        {
            if (validateForm()){
                var xmlhttp = new XMLHttpRequest();
                var notification = document.getElementById("notification").value;
                var city = document.getElementById("city").value;

                if (notification == 1)
                {
                    var data = { "city": city };
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                            var response = JSON.parse(this.responseText);
                            if (response.success)
                            {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Mail send successfully.',
                                    text: 'Mail send successfully.',
                                });
                            }
                        }
                    };
                    xmlhttp.open("POST", "send_mail.php", true);
                    xmlhttp.setRequestHeader("Content-type", "application/json");
                    xmlhttp.send(JSON.stringify(data));
                }

                document.getElementById("searchForm").reset();
            }
        }
        
        function submitForm(e) {
            e.preventDefault();

            if (validateForm()) {
                var formData = new FormData(document.getElementById("searchForm"));
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        var response = JSON.parse(xmlhttp.responseText);

                        if (response.success) {
                            var todayWeather = response.todayWeather;
                            var fiveDayWeather = response.fiveDayWeather;
                            var currWea = document.getElementById("curr-wea");
                            currWea.innerHTML = `
                                <p>The weather in ${todayWeather.name} is currently ${todayWeather.weather}</p>
                                <p>The temperature is ${todayWeather.temperature}°C</p>
                                <p>Humidity is ${todayWeather.humidity}%</p>
                                <p>Wind Speed is ${todayWeather.windSpeed} mph</p>
                            `;

                            document.getElementById("city").value = todayWeather.name;

                            var tableBody = document.querySelector("#weather-container tbody");
                            tableBody.innerHTML = "";
                            var limit = 6;

                            for (var i = 1; i < Math.min(fiveDayWeather.length, limit); i++) {
                                var weather = fiveDayWeather[i];
                                var newRow = tableBody.insertRow();
                                var cell = newRow.insertCell(0);
                                var weatherIcon = document.createElement("img");
                                weatherIcon.src = `https://openweathermap.org/img/wn/${weather.icon}@2x.png`;
                                weatherIcon.alt = `${weather.weather}`;
                                cell.appendChild(weatherIcon);
                                newRow.insertCell(1).innerText = weather.date;
                                newRow.insertCell(2).innerText = weather.name;
                                newRow.insertCell(3).innerText = weather.temperature + "°C";
                                newRow.insertCell(4).innerText = weather.temp_like + "°C";
                                newRow.insertCell(5).innerText = weather.humidity + "%";
                                newRow.insertCell(6).innerText = weather.weather;
                                newRow.insertCell(7).innerText = weather.windSpeed + " mph";
                            }
                            sendEmail();
                        } 
                        else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Search failed!',
                                text: response.message,
                            });
                        }
                    }
                };

                xmlhttp.open("POST", "search_weather.php", true);
                xmlhttp.send(formData);
            }
        }

        

        function changeNotification(e){
            e.preventDefault();

            var notification = document.getElementById("notification").value;
            
            if(notification == 0){
                Swal.fire({
                    title: '您並未訂閱郵件通知，需要開啟通知嗎?',
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: '是!我需要郵件通知!',
                    denyButtonText: '忍痛拒絕!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        var data = { "notification": 1 };
                        var xmlhttp = new XMLHttpRequest();
                        xmlhttp.onreadystatechange = function() {
                            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                Swal.fire('Saved!', '', 'success')
                                document.getElementById("notification").value = 1;
                            }
                        };
                        xmlhttp.open("POST", "update_notification.php", true);
                        xmlhttp.setRequestHeader("Content-type", "application/json");
                        xmlhttp.send(JSON.stringify(data));
                    } 
                    else if (result.isDenied) {
                        Swal.fire('Changes is not saved', '', 'info')
                    }
                })
            }
            else{
                Swal.fire({
                    title: '您已訂閱郵件通知，需要關閉通知嗎?',
                    showDenyButton: true,
                    showCancelButton: false,
                    confirmButtonText: '保留郵件通知!',
                    denyButtonText: '拒絕郵件通知!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire('Changes are not saved', '', 'info')
                    } else if (result.isDenied) {
                        var data = { "notification": 0 };
                        var xmlhttp = new XMLHttpRequest();
                        xmlhttp.onreadystatechange = function() {
                            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                Swal.fire('Saved!', '', 'success')
                                document.getElementById("notification").value = 0;
                            }
                        };
                        xmlhttp.open("POST", "update_notification.php", true);
                        xmlhttp.setRequestHeader("Content-type", "application/json");
                        xmlhttp.send(JSON.stringify(data));
                    }
                })
            }
        }

        window.addEventListener('DOMContentLoaded', function() {
            document.getElementById("searchForm").addEventListener("submit", submitForm);
            // document.getElementById("searchForm").addEventListener("submit", sendEmail);
        });

        window.addEventListener('DOMContentLoaded', function() {
            document.getElementById("nof").addEventListener("click", changeNotification);
        });
    </script>
</head>
<body>
    <?php if ($isLoggedIn): ?>
        <header>
            <nav>
                <h1>What Weather</h1>
                <span class="acc">Account: <?php echo $_SESSION['username']; ?></span>
                <ul>
                    <li><a href="logout.php">Logout</a></li>
                    <li><a href="#" id="nof">Email Notification</a></li>
                </ul>
            </nav>
            <div class="title">
                <h2>Weather Forecast</h2>
            </div>
        </header>
        <main>
            <form name="searchForm" id ="searchForm" method="post" onsubmit="return false;">
                <input type="text" id="city" name="city" placeholder="Enter city name" required>
                <input type="hidden" id="notification" value="<?php echo $_SESSION['notification']; ?>">
                <button type="submit">Search</button>
            </form>
            <h2>天氣查詢系統</h2>
            <div id ="curr-wea">

            </div>   
            <div id="weather-container">
                <table>
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
                    <tbody>
                    </tbody>
                </table>
            </div>
        </main>
    <?php else: ?>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: 'Please login!',
                allowOutsideClick: false,
                willClose: function() {
                    window.location.href = 'index.php';
                }
            }).then(function() {
                window.location.href = 'index.php';
            });
        </script>
    <?php endif; ?>
</body>
</html>