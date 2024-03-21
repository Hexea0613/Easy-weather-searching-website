<?php
  $server_name = '140.123.102.94:3306';
  $username = '611410081';
  $password = 'Y-[T7w2f)er]6T*m';
  $db_name = '611410081';

  $conn = new mysqli($server_name, $username, $password, $db_name);

  if (!empty($conn->connect_error)) {
    die('資料庫連線錯誤:' . $conn->connect_error);
  }

  $conn->query('SET NAMES UTF8');
  $conn->query('SET time_zone = "+8:00"');
?>