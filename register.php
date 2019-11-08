<?php
    session_start();
    include('database.php'); // stores all database info 
    $email = '';
    $password = '';
    $name = '';
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
        $password =  $_POST['password'];
        $name = $_POST['name'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$email) {
            $error = true;
            $error_message[] = 'Please provide a valid email address <br>';
        }

        if(!$password) {
            $error = true;
            $error_message[] =  "Please enter a password <br>";
        }

        elseif ($email && $password && strlen($password) < 8) {
            $error = true;
            $error_message[] =  "Your password should be over 8 characters <br>";
        }

        elseif(!preg_match("#[0-9]+#",$password)) {
            $error = true;
            $error_message[] = "Your Password Must Contain At Least 1 Number!";
        }

        else {
            //sanitising email first for use in query
            $clean_email = mysqli_real_escape_string($db_connection,$email);

            //check whether email is already registered
            $query1 = "SELECT * FROM `accounts` WHERE `email` = '$clean_email'";
            $result1 = mysqli_query($db_connection, $query1);
 
            if (mysqli_num_rows($result1) > 0) {
                die('You have already registered. <a href="log-in.php"> Log in Here </a>');
            } 

            // creating unique code/user
            $vkey = md5(time().$email.'rfdpurgpug'.rand(1000, 999999));

            //sanitize form data 
            $clean_name = mysqli_real_escape_string($db_connection, $name);
            $clean_password = mysqli_real_escape_string($db_connection, $hashed_password);
            $clean_activation_code = mysqli_real_escape_string($db_connection, $vkey);


            //Put account into database
            $query = "INSERT INTO `accounts` (`name`, `email`, `password`, `vkey`, `verified`) VALUES('$clean_name','$clean_email', '$clean_password', '$clean_activation_code', 0);";

            $result = mysqli_query($db_connection, $query);

            if ($result){
                // query ran okay
                if (mysqli_affected_rows($db_connection) == 1){
                    // and we changed 1 or more rows of data
                    $success = true;

                    // sending the email
                    $subject = 'Hi,  '.$name .'Thank you for registering. Please activate your account';
                    $message = "Please click here to activate your account http://ellie.judd/activate.php?vkey={$clean_activation_code} <br><br>";
                    $headers = "From: Dev Me <team@example.com>\r\n";
                    $headers .= "Reply-To: Help <help@example.com>\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html;\r\n";


                    // mail($email, $subject, $message, $headers);

                    if (mail($email, $subject, $message, $headers))
                    {
                        echo "Message accepted <br><br>";
                    }
                    else
                    {
                        echo "Error: Message not accepted <br><br>";
                    }
            // header("Location: login.php");
                    
                }else{
                    // Uh oh, something went wrong
                    $error = true;
                    $error_message[] = 'Something went wrong with the database';
                }
            }else{
                // Uh oh, query didn't run! A problem with the query
                echo "Uh oh, query didn't run! A problem with the query";
            }

        } 
    }

?>
    

<head>
    <title> Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="customisations.css">

</head>
<body style="padding: 50px;">



<h1>Register Here</h1>
<?php
if($error == true) {
    foreach($error_message AS $message){
        echo $message;
    }
}
if($success == true){ 
    echo '';?>
    <div class="success">
        <h2>Account Created!</h2>
        <p>Check your email to validate it</p>
    </div>
    <?php 
} else {
?>
<form action="register.php" method="POST">

    <label for="text">Name </label><br>
    <input type="text" name="name" id="name" value="<?php echo $name; ?>">
    <br><br>

    <label for="email">Email Address </label><br>
    <input type="text" name="email" id="email" value="<?php echo $email; ?>">
    <br><br>

    <label for="password">Password: </label><br>
    <input type="password" name="password"> 
    <br><br>

    <button type="submit">Register</button>
    <p class="log-option">Forgotten your Password? <a href="forgot-password.php"> Reset it here</a></p>
    <p class="log-option">Already Registered? <a href="log-in.php"> Log in here</a></p>
</form>

<?php 
    }
?>