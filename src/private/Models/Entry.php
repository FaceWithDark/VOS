<?php

# Not so much like static types, but at least it does feel better having this here
declare(strict_types=1);

require __DIR__ . '/../Controllers/Controller.php';

// Preset database configurations
$serverHost           = getenv(name: 'DATABASE_HOST', local_only: true) ?: getenv(name: 'DATABASE_HOST');
$databaseName         = getenv(name: 'DATABASE_NAME', local_only: true) ?: getenv(name: 'DATABASE_NAME');
$databaseUser         = getenv(name: 'DATABASE_USER', local_only: true) ?: getenv(name: 'DATABASE_USER');
$databasePassword     = getenv(name: 'DATABASE_PASSWORD', local_only: true) ?: getenv(name: 'DATABASE_PASSWORD');

$databaseCharacterSet = getenv(name: 'DATABASE_CHARACTER_SET', local_only: true) ?: getenv(name: 'DATABASE_CHARACTER_SET');
$databaseCollation    = getenv(name: 'DATABASE_COLLATION', local_only: true) ?: getenv(name: 'DATABASE_COLLATION');

$dataSourceName = "mysql:host=$serverHost; \
                   dbname=$databaseName; \
                   charset=$databaseCharacterSet; \
                   collation=$databaseCollation";

// PDO connection error handling
$errorHandlingOption = [
    PDO::ATTR_ERRMODE               => PDO::ERRMODE_EXCEPTION,  // Set error mode to exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE    => PDO::FETCH_ASSOC,        // Set default fetch mode to associative array
    PDO::ATTR_EMULATE_PREPARES      => false,                   // Use native prepared statements
    PDO::ATTR_PERSISTENT            => true,                    // Keeps database connection alive across scripts
];

// Handling database connection
try {
    $phpDataObject = new PDO(
        dsn: $dataSourceName,
        username: $databaseUser,
        password: $databasePassword,
        options: $errorHandlingOption
    );
    // echo "Connection success!!";

    // Garbage collecting method (recommended from PHP page)
    $phpDataObject = null;
} catch (PDOException $exception) {
    // Failed to connect the database
    die("Connection failed!! Reason: " . $exception->getMessage());
}
