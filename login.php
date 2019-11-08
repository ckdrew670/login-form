<?php

// bring in things from other files that will be needed

include('database.php');
$error = false; // start assuming no error
$error_messages = []; // empty array to store error messages
$success = false; // start by assuming success is false




// STEPS NEEDED

// 1. user types in email and password
// 2. need to check that they have typed something into both fields
    // if not need an error message 'Please enter valid email/password in both fields'
// 3. need to check that their email and password matches database
    // if not need an error message 'Your email and/or password does not match our records'
// 4. need to check that their account is active (activation_status = 1)
    // if not need an error message 'You have not activated your account. Please check your emails for a verification link'


// 2. check they have typed a valid email and password and return errors

if ($_POST) {
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    // check they have entered an email and password into the fields

    if ($email && $password) {
        // check for valid email

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
           
            // 3. Check their email and password are in database
    
            // email
            $query_email = "SELECT * FROM `users` WHERE `email` = '$email';";
            $result_email = mysqli_query($db_connection, $query_email);

            if (mysqli_num_rows($result_email) > 0) {
                // good, their emails match

                $row = mysqli_fetch_assoc($result_email);

                // 4. check account is activated (activation_status = 1)
                if ($row["activation_status"] == '1') {

                    // check password: remember to unhash the password to check it
                    if (password_verify($password, $row["password"])){

                        $success = true;


                    } else {

                        $error = true;
                        $error_messages[] = "The email address and password you have entered are not on our records. Try again.";
                    }
                
                } else {

                    $error = true;
                    $error_messages[] = "You have not activated your account. Check your email for a verification link.";
                }

            } else {

                $error = true;
                $error_messages[] = "The email address and password you have entered are not on our records. Try again.";
            }
            
        } else {
            
            $error = true;
            $error_messages[] = "Please enter a valid email address and password.";
        }
    

    } else {

        $error = true;
        $error_messages[] = "Please enter a valid email address and password.";
    }
    
} 

?>

<!-- HTML -->

<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <meta charset="utf-8">
        <title>Log In</title>
    </head>
    <body>

        <header>
            <h1>Log In</h1>
        </header>

        
        <?php
        // do something if it works
            if ($success){

                // redirect to account.php

                header('Location: account.php');

                // would use sessions or cookies both here for demo purposes... 

                // SESSIONS
                // starts new session, creating a unique session id
                // also creates new file on server that stores the session info
                session_start(); // start session

                $_SESSION['logged_in'] = 'YES'; // use session
                    
                // COOKIES
                // or use cookies (don't use both - only here for demo purposes)
                // setcookie ('logged_in', 'YES', time()+3600);


            }
        ?>

        <section class="centred">

        <?php 
        // logout redirection (if they've come from the logout page)

            if (isset($_GET['logged-out'])){

                echo "<h2 class='success'>You have successfully logged out.</h2>";
            }
        ?>

        <?php 
        
        // create error message if an email or password is not entered correctly
        
            if (isset($error)){
                foreach($error_messages AS $message){
                    echo '<p class="error">'.$message.'</p>';
                }
            }
           
        ?>
        </section>

        <form action="login.php" method="post">
            
            <label>Email:</label>
            <input type="text" name="email" class="input" placeholder="e.g. jo.bloggs@hotmail.com"/>
            
            <label>Password:</label>
            <input type="password" name="password" class="input"/>

    
            <input class="button" type="submit" name="submit" value="Login"/>

            <p><a href="register.php">Haven't registered with us yet? Create an account</a></p>

        </form>
        
      
            
        

    </body>

</html>