<?php 
    // These variables define the connection information
    $username = "stepshep"; 
    $password = "menyoucs110"; 
    $host = "users.csss5n4ctp7b.us-east-1.rds.amazonaws.com"; 
    $dbname = "innodb"; 

    // By passing the following $options array to the database connection code we 
    // are telling the MySQL server that we want to communicate with it using UTF-8 
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
    
    try 
    { 
        $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options); 
    } 
    catch(PDOException $ex) 
    { 
        die("Failed to connect to the database: " . $ex->getMessage()); 
    } 
     
    // This statement configures PDO to throw an exception when it encounters 
    // an error.
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
     
    // This statement configures PDO to return database rows from your database using an associative 
    // array. 
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
     
    // This block of code is used to undo magic quotes.
    if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) 
    { 
        function undo_magic_quotes_gpc(&$array) 
        { 
            foreach($array as &$value) 
            { 
                if(is_array($value)) 
                { 
                    undo_magic_quotes_gpc($value); 
                } 
                else 
                { 
                    $value = stripslashes($value); 
                } 
            } 
        } 
     
        undo_magic_quotes_gpc($_POST); 
        undo_magic_quotes_gpc($_GET); 
        undo_magic_quotes_gpc($_COOKIE); 
    } 
     
    // This tells the web browser that your content is encoded using UTF-8 
    // and that it should submit content back to you using UTF-8 
    header('Content-Type: text/html; charset=utf-8'); 
     
    // This initializes a session.
    session_start(); 

        // Ensure that the user has entered a non-empty password 
        if(!isset($_GET['passhash'])) 
        { 
            //die("Please enter a password.");
            $outputPass = array('Status' => "Error", 'Message' => "Please enter a passhash");

            echo json_encode($outputPass);
            die();

        } 
         
        // Make sure the user entered a valid E-Mail address 
        if(!filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) 
        { 
            //die("Invalid E-Mail Address"); 
          $outputEmail = array('Status' => "Error", 'Message' => "Please enter a valid email");

          echo json_encode($outputEmail);
          die();
        } 

         
        // Now we perform the same type of check for the email address, in order 
        // to ensure that it is unique. 
        $query = " 
            SELECT 
                1 
            FROM users 
            WHERE 
                email = :email 
        "; 
         
        $query_params = array( 
            ':email' => $_GET['email'] 
        ); 
         
        try 
        { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            die("Failed to run query: " . $ex->getMessage()); 
        } 
         
        $row = $stmt->fetch(); 
         
        if($row) 
        { 
          $outputEmail2 = array('Status' => "Error", 'Message' => "This email is already registered");

          echo json_encode($outputEmail2);
          die();
        } 
         
         if(isset($_GET['business']))
         {
            $query = " 
                INSERT INTO users ( 
                    email,
                    passhash,
                    session,
                    business
                ) VALUES ( 
                    :email, 
                    :passhash, 
                    :session,
                    :business
                ) 
            "; 
         }
         else {
            $query = " 
                INSERT INTO users ( 
                    email,
                    passhash, 
                    session 
                ) VALUES ( 
                    :email, 
                    :passhash, 
                    :session 
                ) 
            "; 
        }
         
    $bytes = openssl_random_pseudo_bytes(12, $cstrong);
    $hex   = bin2hex($bytes);
         
        // Here we prepare our tokens for insertion into the SQL query.
        if(isset($_GET['business']))
        {
                    $query_params = array( 
            ':email' => $_GET['email'], 
            ':passhash' => $_GET['passhash'], 
            ':session' => $hex,
            ':business' => $_GET['business']
        ); 
        }
        else {
            $query_params = array( 
                ':email' => $_GET['email'], 
                ':passhash' => $_GET['passhash'], 
                ':session' => $hex 
            );
        }
         
        try 
        { 
            // Execute the query to create the user 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            // Note: On a production website, you should not output $ex->getMessage(). 
            // It may provide an attacker with helpful information about your code.  
            die("Failed to run query: " . $ex->getMessage()); 
        } 
         
            $_SESSION['user'] = $row;
            
            if(isset($_GET['business']))
                $outputGood = array('Status' => "Success", 'SessionID' => $hex, 'Business' => $_GET['business'], 'Kosher' => 0, 'Vegetarian' => 0, 'Vegan' => 0, 'Peanut-Allergy' => 0, 'Gluten-Free' => 0, 'Dairy-Free' => 0, 'Low-Fat' => 0);
            else
                $outputGood = array('Status' => "Success", 'SessionID' => $hex,  'Kosher' => 0, 'Vegetarian' => 0, 'Vegan' => 0, 'Peanut-Allergy' => 0, 'Gluten-Free' => 0, 'Dairy-Free' => 0, 'Low-Fat' => 0);

            echo json_encode($outputGood);
            die();