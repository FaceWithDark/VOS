<?php
    require_once __DIR__ . '/../vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Database configuration
    $serverHost = $_ENV['DATABASE_HOST'];
    $databaseName = $_ENV['DATABASE_NAME'];
    $databaseUsername = $_ENV['DATABASE_USER'];
    $databasePassword = $_ENV['DATABASE_PASSWORD'];

    // PDO options for maximum error handling
    $fullErrorHandlingOption = [
        PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,  // Set error mode to exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,        // Set default fetch mode to associative array
        PDO::ATTR_EMULATE_PREPARES      => false,                   // Use native prepared statements
        PDO::ATTR_PERSISTENT            => true,                    // Keeps database connection alive across scripts
        PDO::MYSQL_ATTR_INIT_COMMAND    => 'SET NAMES utf8mb4'      // Ensure charset is set to utf8mb4 (same as database)
    ];

    // Create PDO instance
    try {
        $phpDataObject = new PDO("mysql:host=$serverHost;dbname=$databaseName;charset=utf8mb4", $databaseUsername, $databasePassword, $fullErrorHandlingOption);
        // echo "Connected successfully";

    } catch (PDOException $exception) {
        // Failed to connect the database
        die("Connection failed: " . $exception -> getMessage());
    }
?>
