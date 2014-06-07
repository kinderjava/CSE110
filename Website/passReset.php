<?php 
    // These variables define the connection information
    $username = "stepshep"; 
    $password = "menyoucs110"; 
    $host = "users.csss5n4ctp7b.us-east-1.rds.amazonaws.com"; 
    $dbname = "innodb"; 
    
    if(!isset($_POST["email"]) || !isset($_POST["reset"]) || !isset($_POST["pass"]))
    {
	    echo "Invalid parameters";
	    die();
    }

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
     
        // This query retrieves the user's information from the database using 
        // their email. 
        $query = " 
            SELECT 
                email,
                reset
            FROM users 
            WHERE 
                email = :email 
        "; 
         
        // The parameter values 
        $query_params = array( 
            ':email' => $_POST['email'] 
        ); 

       $email = $_POST['email'];
         
        try 
        { 
            // Execute the query against the database 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            // Note: On a production website, you should not output $ex->getMessage(). 
            // It may provide an attacker with helpful information about your code.  
            die("Failed to run query: " . $ex->getMessage()); 
        } 
         
        // This variable tells us whether the user has successfully logged in or not. 
        // We initialize it to false, assuming they have not. 
        // If we determine that they have entered the right details, then we switch it to true. 
        $login_ok = false; 
         
        // Retrieve the user data from the database.  If $row is false, then the email 
        // they entered is not registered. 
        $row = $stmt->fetch(); 
        if($row) 
        { 
            if(($_POST['reset'] != "") && ($_POST['reset'] != "0") && ($_POST['reset'] === $row['reset'])) 
            { 
                // If they do, then we flip this to true 
                $login_ok = true;
            }
            else {
          echo "Invalid reset token, please try again";
          die();
         }
        } 
         
        if($login_ok) 
        { 
    $bytes = openssl_random_pseudo_bytes(12, $cstrong);
    $hex   = bin2hex($bytes);

       // Initial query parameter values 
        $query_params = array( 
            ':pass' => hash("sha256", $_POST['pass']) 
        ); 

        $query = " 
            UPDATE users 
            SET 
                reset = '0',
                passhash = :pass
            WHERE 
                email = '$email'  
        ";
        

        try 
        { 
            // Execute the query 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params);
        } 
        catch(PDOException $ex) 
        { 
            // Note: On a production website, you should not output $ex->getMessage(). 
            // It may provide an attacker with helpful information about your code.  
            die("Failed to run query: " . $ex->getMessage()); 
        }
        echo "Password Reset";
        } 
        else 
        { 
          echo "Invalid email";
          die();
        }