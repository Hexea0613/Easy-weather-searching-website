<?php
// Initialize the session
	session_start();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>登入介面</title>
    	<link href="css/index.css" rel="stylesheet">
	</head>
	<body>
		<div class ="container">
			<h1>Weather Forecast</h1>
			<h2>你可以選擇登入或是註冊帳號</h2>
			<button onclick="goToPage('login.html')">Login</button>
			<button onclick="goToPage('register.html')">Register</button>
		</div>
		<script>
			function goToPage(url) {
				window.location.href = url;
			}
    	</script>
	</body>
</html>
