 <?php

 session_start();
 include('database.php');
 $_SESSION['logged_in'] = 'NO';

 header('Location: log-in.php')
?>

<html> 
    <head>
        <title>Logging Out</title>
    </head>
    <body>
        <h1>Logging You Out.. </h1>
    </body>
</html>