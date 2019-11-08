
<!-- Form
PHP form handling 
Check user input
Create an activation code
Save in database (will need to CREATE TABLE first)
Send email
Account creation success message
 -->

<?php 
include('database.php');

// form handling
$error = false; // start assuming no error
$error_messages = []; // empty array to store error messages
$confirm = "Check your email for your activation link.";
$success = false; // start by assuming success is false
    
    
    //1. set up some variables to store information inputted via the form 

    if($_POST)  

    {

        $email = $_POST["email"];
        $password = $_POST["password"];
        

    
        // check they have entered an email and password into the fields

        if ($email && $password) 
        
        {
            // check for valid email

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
            {
                $error = true;
                $error_messages[] = 'Please enter a valid email address.';

            }

            // check for valid password

            // must be at least 8 character and contain at least one upper case letter and one number.

            if (strlen($password) < 8) 
            {
                $error = true;
                $error_messages[] = "Password too short!";
            }
        
            if (!preg_match("#[A-Z0-9]+#", $password)) 
            {
                $error = true;
                $error_messages[] = "Password must include at least one number and one upper case letter!";
            }

            // if they enter an email and password then we want to create an activation code to send to their email

            // to get their email we can grab it from the 'email' input

            //email contains a link with the unique activation code in the url
            

        } else 
        
        {
            $error = true;
            $error_messages[] = "Please enter a valid email address and password.";
        }



        if (!$error){
            // no problem so far, continue

            // activation code
            // unique (random)
            // hard to guess (random/long)
            
            $activation_code = hash("sha256",$password.rand(1,999999));

            
            // sanitise database

            $clean_email = mysqli_real_escape_string($db_connection, $email);
            
            // hash the password to make it more secure
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            
            $clean_activation_code = mysqli_real_escape_string($db_connection, $activation_code);

            /*
            CREATE TABLE `users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `email` varchar(255) NOT NULL,
            `password` varchar(255) NOT NULL,
            `activation code` varchar(255) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
            */

            // query building

            $query = "INSERT INTO `users` (`email`, `password`, `activation_code`, `activation_status`) VALUES ('$clean_email', '$hashed_password', '$clean_activation_code','0');";

            // run query

            $result = mysqli_query($db_connection, $query);

            // check result

            if ($result){
                // query ran okay
                
                if (mysqli_affected_rows($db_connection) == 1){
                    // and we changed 1 or more rows of data

                    $link = '<a href="http://192.168.33.10/login-form/activation.php?activation_code='.urlencode($activation_code).'">link</a>'; // <-- url encode makes sure the code can be used in a URL

                    // send email
                    $to_email = $email;
                    $subject = "Activate Your Account";
                    $message = "Hello<br/>Follow this ".$link." to activate your account.<br>Best wishes<br/>Dev Me Team.";

                    $headers = "From: Dev Me <team@example.com>\r\n";
                    $headers .= "Reply-To: Help <help@example.com>\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html;\r\n";

                    if (mail($to_email, $subject, $message, $headers)) {
                        //mail sent
                        $success = true; 
                    } else {
                        // something went wrong, email didn't send
                        $error = true;
                        $error_messages[] = 'Something went wrong sending the email';
                    }


                    // show a success message
                    $success = true;

                

                } else {
                    // Uh oh, something went wrong send an error message
                    $error = true;
                    $error_messages[] = "There's something wrong with the database.";
                }
            }else{
                // Uh oh, query didn't run! A problem with the query
                $error = true;
                $error_messages[] = "There's something wrong with the database.";
            }
              
        }

    }
?>

<html>
    <head>
        <link rel="stylesheet" href="style.css">
        <meta charset="utf-8">
        <title>Register</title>
    </head>
    <body>

        <header>
            <h1>Register</h1>
        </header>
        <section class="centred">
        <?php

            if ($success) 
            
            {
                echo '<h2 class="success">We created your account! Now check your email.</p>';
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
            
        <form action="register.php" method="post">
            
            <label>Email:</label>
            <input type="text" name="email" class="input" placeholder="e.g. jo.bloggs@hotmail.com"/>
            
            <label>Password:</label>
            <input type="password" name="password" class="input"/>

    
            <input class="button" type="submit" name="submit" value="Create Account"/>

            
        
        </form>

    </body>

</html>