<?php
    session_start();
    include('database.php'); // stores all database info 
    $email = '';
    $password = '';
    $error = false;//assume no errors yet
    $error_message =[]; //empty array for error messages
    $success = false;

    // Maybe useful bits?
    //redirect to another page on successful login 
    // header('Location: welcome.php');

    $message = '';

    if (isset($_SESSION['logged_in'])){
        if ('YES' == $_SESSION['logged_in']){
            ?> <p>You are already logged in. Click <a href="account-home.php"> here </a>to go to your account home page or click <a href="log-out.php"> here</a> to log out</p> <?php
        }
    }
    if ($_POST) { 
        $email =  $_POST['email'];  
        $clean_email = mysqli_real_escape_string($db_connection,$email);
        $success = false;
        $registered = '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$email) {
            $error = true;
            $error_message[] = 'Please provide a valid email address <br>';
        }

        else {
            //sanitising email first for use in query
            $clean_email = mysqli_real_escape_string($db_connection,$email);

            //check whether email is already registered
            $query1 = "SELECT * FROM `accounts` WHERE `email` = '$clean_email'";
            $result1 = mysqli_query($db_connection, $query1);
 
            //if the email is registered...
            if (mysqli_num_rows($result1) == 0) {
                $error = true;
                $error_message[] = 'Please check email is registered and try again';
            }

            elseif (mysqli_num_rows($result1) > 0) {

                // creating a new unique code/vkey
                $vkey = md5(time().$email.'rfdpurgpug'.rand(1000, 999999));
                //sanitize form data 
                $clean_activation_code = mysqli_real_escape_string($db_connection, $vkey);

                //Put account into database
                $query = "UPDATE `accounts` SET `vkey` = '$clean_activation_code' WHERE `email` = '$clean_email';";

                $result = mysqli_query($db_connection, $query);

                if ($result){
                    // query ran okay
                    if (mysqli_affected_rows($db_connection) == 1){
                        // and we changed 1 or more rows of data
                        $success = true;

                        // sending the email
                        $subject = 'Your password reset request';
                        $message = " Please follow this link to reset your password http://ellie.judd/reset.php?vkey={$clean_activation_code} <br><br>";
                        $headers = "From: Dev Me <team@example.com>\r\n";
                        $headers .= "Reply-To: Help <help@example.com>\r\n";
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-Type: text/html;\r\n";

                        $sendemail = mail($email, $subject, $message, $headers);

                        if ($sendemail)
                        {
                             echo "Email sent <br><br>";
                        }
                        else
                        {
                            echo "Error: Email not sent <br><br>";
                        }
                        // header("Location: login.php");
                        
                }else{
                    // Uh oh, something went wrong
                    $error = true;
                    $error_message[] = 'Please check email is valid and try again';
                }
            }else{
                // Uh oh, query didn't run! A problem with the query
                echo "Uh oh, query didn't run! A problem with the query";
            }

        } 
    }
    }
?>
    

<head>
    <title>Forgotten Password</title>
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

<h1>Forgotten your Password?</h1>
<?php
if($error == true) {
    foreach($error_message AS $message){
        echo $message;
    }
}
if($success == true){ 
    echo '';?>
    <div class="success">
        <h2>Reset link sent!</h2>
        <p>Check your email to reset your password</p>
    </div>
    <?php 
} else {
?>
<form action="" method="POST">

    <label for="email">Email Address </label><br>
    <input type="text" name="email" id="email" value="<?php echo $email; ?>">
    <br><br>

    <button type="submit">Send me a reset link</button>

</form>

<?php 
    }
?>