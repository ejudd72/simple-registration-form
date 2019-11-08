<?php
    session_start();
    include('database.php'); // stores all database info 
    //set defaults
    $email = '';
    $error = false;
    $success = false;
    $error_message = [];
    $password = '';

    //checking if user is already logged in
    $loggedin = false;
    if (isset($_SESSION['logged_in'])){
        if ('YES' == $_SESSION['logged_in']){
            $loggedin = true;
        }
        else {
            $loggedin = false;
        }
    }

    if(isset($_GET['vkey'])){
        
        //getting code from activation link and cleaning it
        $vkey = $_GET['vkey'];
        $clean_vkey = mysqli_real_escape_string($db_connection, $vkey);

        //Checking to see if the code matches that on the database
        $query = "SELECT * FROM `accounts` WHERE `vkey` = '$clean_vkey';";

        $result = mysqli_query($db_connection, $query);

        if (mysqli_num_rows($result) > 0){

            if ($_POST) {

                $password =  $_POST['password'];
               
                if(!$password) {
                    $error = true;
                    $error_message[] =  "Please enter a password <br>";
                    echo "Please enter a password <br>";
                }   
                
                if (strlen($password) < 8) {
                    $error = true;
                    $error_message[] =  "Your password should be over 8 characters <br>";
                    echo "Your password should be over 8 characters <br>";
                }    
                
                if(!preg_match("#[0-9]+#",$password)) {
                    $error = true;
                    $error_message[] = "Your Password Must Contain At Least 1 Number!";
                    echo "Your Password Must Contain At Least 1 Number!";
                
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    //our query that changes the verification status
                    $verify = "UPDATE `accounts` SET `password` = '$hashed_password' WHERE `vkey` = '$clean_vkey';";
                    
                    //storing the result of that query in a variable
                    $result = mysqli_query($db_connection, $verify);

                    if ($result) {
                        //the result happened, the query is ok!

                        if(mysqli_affected_rows($db_connection) == 1) {
                            $success = true;
                            //we also changed 1 or more rows of data 
                            


                        } else {
                            // Query didn't run: a problem with the query
                            $error = true;
                            $error_message[] = 'Something went wrong with the database';
                        }
                    }
                }

            } else {
                // Uh oh, query didn't run! A problem with the query
                $error = true;
                $error_message[] = 'Something went wrong with the database';
            }
        
        } else { 
            $error = true;
            $error_message[] = 'You don\'t have an activation code. Try following the link again.';
        } 
    }

 ?>


<html> 

    <head>
        <title>Account Activation</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="customisations.css">

    </head>
        <body style="padding: 50px;">
        <?php 
            if ('YES' == $_SESSION['logged_in']){
                ?>
            
                <img  class="log" src="loggedin.svg" alt="">
            
            <?php } else { ?>
            
                <img class="log" src="loggedout.svg" alt="">
            
            <?php } ?>

        <?php if($success == false){ ?>
        <form action="" method="POST">
        <h1>Reset your Password Here</h1>
            <label for="password">New Password: </label><br>
            <input type="password" name="password"> 
            <br><br>
            <button type="submit">Reset my Password</button>
            </form>
            
        <?php }

       elseif($success == true){
           ?>
            <div class="success">
                <h2>Password Successfully Reset!</h2>
                <p class="log-option"><a href="log-in.php">Log in Here</a></p>
            </div>

            <?php 
            } else {
                if ($error == true){
                    foreach($error_message AS $message){
                        echo '<p class="error">'.$message.'</p>';
                    }
                }
            } 
        ?>
    </body>
</html>
