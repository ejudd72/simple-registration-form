<?php
session_start();
include('database.php');
$email = '';
$success = false;
$stored_pass = '';
$error = false;

if (isset($_SESSION['logged_in'])){
    if ('YES' == $_SESSION['logged_in']){
        ?> <p>You are already logged in. Click <a href="account-home.php"> here </a>to go to your account home page or click <a href="log-out.php"> here</a> to log out</p> <?php
    }
} 

if($_POST){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $clean_email = mysqli_real_escape_string($db_connection,$email);
    $password = mysqli_real_escape_string($db_connection, $password);

    $query = ("SELECT * FROM `accounts` WHERE `email` = '$clean_email';");

    $result = mysqli_query($db_connection, $query);

    if (mysqli_num_rows($result) > 0){
        $verified = ("SELECT * FROM `accounts` WHERE `email` = '$clean_email' AND `verified` = 1;");

        $verified_result = mysqli_query($db_connection, $query);

        if (mysqli_num_rows($verified_result) == 0) {
            echo $verified_result . '<br>';
            $error_message[] = 'Incorrect username or password';
            $error = true;
        
        } else {

            while($row = mysqli_fetch_assoc($result)){
                $stored_pass = $row['password'];
                echo 'checking password' . '<br>';
            };

            if (password_verify($password, $stored_pass)){
                echo 'creating new vkey ' . '<br>';
                
                // creating a new unique code/vkey (to take user to their unique page)
                $vkey = md5(time().$email.'rfdpurgpug'.rand(1000, 999999));

                //query to update the vkey for unique page
                $changekey = "UPDATE `accounts` SET `vkey` = '$vkey' WHERE `email` = '$clean_email';";
            
                $result = mysqli_query($db_connection, $changekey);
                echo $result;

                if($result){
                    $success = true;
                    echo 'database worked '. '<br>';
                    $_SESSION['logged_in'] = 'YES';
    
                    header("Location: account-home.php?user={$vkey}");
                    exit;
                    // echo 'logged in';

                } else {
                    $error_message[] = 'Issue with the database'; 
                    $error = true;
                }

            } else {
                $error_message[] = 'Incorrect username or password'; 
                $error = true;
            }
        }

    }  else {
        $error_message[] = 'Incorrect username or password';
        $error = true;
    }
}
?>

<html> 

<head>
    <title>Log In</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="customisations.css">
</head>
<body style="padding: 50px;">
    <?php 
        if ($error == true) {
            foreach($error_message AS $message){
                echo $message;
            }
        } ?>

        <h2>Please Log In Below</h2>

        <form action="" method="POST">

            <label for="email">Email Address </label><br>
            <input type="text" name="email" id="email" value="<?php echo $email; ?>">
            <br><br>

            <label for="password">Password: </label><br>
            <input type="password" name="password"> 
            <br><br>

            <input type="submit" value="Log in">
        </form>

        <p class="log-option">Don't have an account? <a href="register.php"> Register Here</a></p>
        <p class="log-option">Forgotten your Password? <a href="forgot-password.php"> Reset it here</a></p>
        
    </body>
