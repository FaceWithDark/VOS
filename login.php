<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
    <body>
        <form action="login.php" method="post">
            <!-- Login section -->
            <center><label>Login: </label> <br></center>
            <center><input type="text" name="username"> <br></center>
            
            <!-- Password section -->
            <center><label>Password: </label> <br></center>
            <center><input type="password" name="password"> <br><br></center>
            
            <!-- Process to next page -->
            <center><input type="submit" name="continue" value="continue"></center>    
        </form>
    </body>
</html>

<?php
    // Check if the "Continue" button is clicked.
    if(isset($_POST["continue"])) {
        // Check if both username and password has no empty value (has been filled in).
        if(!empty($_POST["username"]) && !empty($_POST["password"])) {
            // Get user's username for this session and stored in cookie sessions (//TODO: need to implement cookies).
            $_SESSION["$username"] = $_POST["username"];
            // Get user's password for this session and stored in cookie sessions (//TODO: need to implement cookies).
            $_SESSION["$password"] = $_POST["password"];
            // Direct to the set location.
            header("Location: index.php");
        } 
        // Check if user has entered an username or password or none.
        else {
            // Show the warning text to user.
            echo "You need to enter your username/password !";
        }
    }
?>