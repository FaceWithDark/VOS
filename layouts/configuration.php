<?php
    require_once __DIR__ . '/../vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Database configuration
    $serverHost = 'db'; // The service name from docker-compose.yml
    $databaseName = $_ENV['DATABASE_NAME'];
    $databaseUsername = $_ENV['DATABASE_USER'];
    $databasePassword = $_ENV['DATABASE_PASSWORD'];

    // PDO options for maximum error handling
    $fullErrorHandlingOption = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Set error mode to exceptions
        PDO::ATTR_EMULATE_PREPARES => false, // Use native prepared statements
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Set default fetch mode to associative array
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' // Ensure charset is set to utf8
    ];

    // Create PDO instance
    try {
        $phpDataObject = new PDO("mysql:host=$serverHost;dbname=$databaseName;charset=utf8", $databaseUsername, $databasePassword, $fullErrorHandlingOption);
        // echo "Connected successfully";

    } catch (PDOException $exceptions) {
        // Failed to connect the database
        die("Connection failed: " . $exceptions -> getMessage());
    }
?>
