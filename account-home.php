<!-- 
1) Form /
2) PHP form handling /
3) Check user input
4) Create an activation code - requirements?
5) Save in database (will need to CREATE TABLE first)
6) Send email
7) Account creation success message
 -->

 <?php
    session_start();
    include('database.php'); 
    $name = 'Beautiful';
    $loggedin = false;
    if (isset($_SESSION['logged_in'])){
        if ('YES' == $_SESSION['logged_in']){
            $loggedin = true;
        }
        else {
            header("Location: log-in.php");
        }
    }

    $vkey = $_GET['user'];

    //our query that changes the verification status
    $query = "SELECT * FROM  `accounts` WHERE `vkey` = '$vkey';";
                
    //storing the result of that query in a variable
    $result = mysqli_query($db_connection, $query);

    if (mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $name = $row['name'];
        }
    }
    
    
?>

<html> 
    <head>
        <title>Account Home</title>
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
            
        <h1>Hello, <?php echo $name; ?>. Welcome to your home page</h1>    

        <h2>look at these lovely pictures</h2>
        <section class="gallery">
            <img src="img-1.jpg" alt="">
            <img src="img-2.jpg" alt="">
            <img src="img-3.jpg" alt="">
            <img src="img-4.jpg" alt="">
            <img src="img-5.jpg" alt="">
            <img src="img-6.jpg" alt="">

        </section>
        <p class="log-option">Click <a href="change-password.php?user=<?php echo $vkey ?> "> here </a> to update your password</p>
        <p class="log-option">Click <a href="log-out.php"> here </a> to log out</p>
    </body>
</html>
