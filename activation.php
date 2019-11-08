
<?php

// bring in things from other files that will be needed
include('database.php');
$error = false; // start assuming no error
$error_messages = []; // empty array to store error messages
$success = false; // start by assuming success is false

// *STEPS NEEDED*:

// 1. need the code from query string 
// 2. need error catching for if there is no code
// 3. need to match with code in database
// 4. if no record then error message
// 5. if do find record, check they haven't already activated account
    // add new column to table that shows if they've activated already
    // set default as 0
    // i.e. activation_status = 0;

// 6. all good then we want to activate account



// 1. need the code from the query string (in the url)

    if (isset($_GET['activation_code'])){

        $activation_code = $_GET['activation_code']; //<-- gets activation code from url
        
        // sanitise it
        $clean_code = mysqli_real_escape_string($db_connection, $activation_code);

    } else {

        // 2. if no code is found then produce an error
        $error = true;
        $error_messages[] = "You do not have an activation code.";
        
    }
        
    // 3. match the code from the url to the code in the database 


        // query the database to look for the code
    $query = "SELECT * FROM `users` WHERE `activation_code` = '$clean_code';"; 

        // run the query
    $result = mysqli_query($db_connection, $query);

        // var_dump($result); <-- can use this to check

        // accessing the result data

    if (mysqli_num_rows($result) > 0){ // <-- if it finds at least one entry in the database

        $row = mysqli_fetch_assoc($result);

            // var_dump($row); < -- can use to check

            // now check whether they have already activated their account

        if ($row["activation_status"] == 0) { // <-- if they haven't

            // need to update the table to set activation_status to 1
            $query = "UPDATE `users` SET `activation_status` = '1' WHERE `activation_code` = '$clean_code';";
            
            $result = mysqli_query($db_connection, $query); // run query

            if ($result){ // <-- if query ran OK
                
                if (mysqli_affected_rows($db_connection) == 1){
                    // and we changed 1 or more rows of data
                    $success = true;

                } else {
                    // Uh oh, query didn't run! A problem with the query
                    $error = true;
                    $error_message[] = 'Something went wrong with the database';
                }

            } else {
                // Uh oh, query didn't run! A problem with the query

                $error = true;
                $error_message[] = 'Something went wrong with the database';
            }

        } else { //<-- if activation_status is not 0

            $error = true;
            $error_messages[] = 'You\'ve already activated your account. Please <a href="login.php">login here</a>.';
        }

    } else { // <-- if not activation code found
        $error = true;
        $error_message[] = 'You don\'t have an activation code. Try following the link again.';
    }
?>  

    <!-- HTML for the success page -->
    
    <head>
        <link rel="stylesheet" href="style.css">
        <meta charset="utf-8">
        <title>Log In</title>
    </head>

    <body>
        <header>
            <h1>Activate</h1>
        </header>

            <section class="centred">

            <?php
            if ($success == true){ ?>
                <h2 style="color:green;">Your account has been activated</h2>
            
                <p>Please <a href="login.php">click here to log in</a></p>
            
            <?php 
            
            } else {

                if ($error == true) {

                    foreach($error_messages AS $message){
                        echo '<p class="error">'.$message.'</p>';
                    }
                }
            }
            ?>
            
            </section>

    </body>
