<?php

# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

// Preset database configurations
$serverHost = $_ENV['DATABASE_HOST'];
$databaseName = $_ENV['DATABASE_NAME'];
$databaseUsername = $_ENV['DATABASE_USER'];
$databasePassword = $_ENV['DATABASE_PASSWORD'];

// PDO connection error handling
$errorHandlingOption = [
    PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,  // Set error mode to exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,        // Set default fetch mode to associative array
    PDO::ATTR_EMULATE_PREPARES      => false,                   // Use native prepared statements
    PDO::ATTR_PERSISTENT            => true,                    // Keeps database connection alive across scripts
    PDO::MYSQL_ATTR_INIT_COMMAND    => 'SET NAMES utf8mb4'      // Ensure charset is set to utf8mb4 (same as database)
];

// Handling database connection
try {
    $phpDataObject = new PDO("mysql:host=$serverHost;dbname=$databaseName;charset=utf8mb4", $databaseUsername, $databasePassword, $errorHandlingOption);
    // echo "Connection success!!";

    // SQL query test to confirm connection works
    $query = "SHOW TABLES;";

    // Trick to avoid SQL injections
    $queryStatement = $phpDataObject->prepare($query);

    // Handling if SQL execution test is correct or not
    if ($queryStatement->execute()) {
        error_log("Statement executed!", 0);
        return true;
    } else {
        error_log("Statement not executed!!", 0);
        return false;
    }

    // Garbage collecting method (recommended from PHP page)
    $query = null;
    $phpDataObject = null;
} catch (PDOException $exception) {
    // Failed to connect the database
    die("Connection failed!! Reason: " . $exception->getMessage());
}
