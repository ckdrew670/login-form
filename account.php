
<?php
// start a session
session_start();


// use same error variables etc.
$error = false; // start assuming no error
$error_messages = []; // empty array to store error messages
$success = false; // start by assuming success is false
 
// SESSIONS
if (isset($_SESSION['logged_in']))
{
    
    if ('YES' == $_SESSION['logged_in'])
    {
        $success = true;
        
    }
} else {

    $error = true;
    $error_messages[] = "You are not logged in. Please <a href='login.php'>try again.</a>";
}

// COOKIES
// if ('YES' == $_COOKIE['logged_in']){

// 	$success = true;
// }

?>
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="utf-8">
    <title>Logged in</title>
</head>

    <header>
        <h1>Your Account</h1>
    </header>

    <section class="centred">
        <?php
        if ($success == true){ ?>
            <h2 class="success">You have successfully logged in.</h2>
        
        
        <?php 
        
        } else {

            if ($error == true) {

                foreach($error_messages AS $message){
                    echo '<h2 class="error">'.$message.'</h2>';
                }
            }
        }
        ?>

        <p><a href="logout.php">Click here to log out.</a></p>
        
    </section>
