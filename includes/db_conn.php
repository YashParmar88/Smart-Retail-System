<?php

/* database configuration */
$host = "localhost";
$user = "root";
$pass = ""; /* Laragon default is empty */
$db_name = "smart_retail_db";

/* connection bridge */
$conn = mysqli_connect($host, $user, $pass, $db_name);

/* check connection status */
if (!$conn) {
    /* show error if connection fails */
    die("Connection failed: " . mysqli_connect_error());
}

// Success means the website can now talk to the database
?>