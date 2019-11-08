<?php 

//connect to server
    $db_servername = "localhost";
    $db_username = "root";
    $db_password = "root";
    $db_database = "scotchbox";
    
    // Create connection
    $db_connection = new mysqli($db_servername, $db_username, $db_password, $db_database);
    
    // Check connection
    if ($db_connection->connect_error) {
        die("Connection failed: " . $db_connection->connect_error);
    }
    // echo "Connected successfully";
?>
