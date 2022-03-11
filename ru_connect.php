<?php

// Initialize connection variables.
$host = "infraplus-ru.org";
$database = "householdvisit";
$user = "postgres";
$password = "postgres@ru1234";
$port = "5432";

$appName = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
//echo $appName . $_SERVER;

//$conn_string = "host=122.155.9.78 port=5432 dbname=cubigtree2019 user=postgres password=postgresadmin";
$conn_string = "host=$host port=5432 dbname=$database user=$user password=$password port=$port";
//echo $conn_string;

$connection = pg_connect( $conn_string );

// if($connection) {
//     echo 'Successfully connected.';
//  } else {
//     $error = error_get_last();
//     echo $error['message'];
//  } 

?>