<?php
// Copy this file to db.php and fill in your database credentials
// cp app/config/db.example.php app/config/db.php

$host     = 'localhost';
$dbname   = 'isdn_db';      // your database name
$username = 'root';          // your MySQL username
$password = '';              // your MySQL password

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
