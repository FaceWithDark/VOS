<?php

declare(strict_types=1);

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Symfony\Component\Dotenv\Dotenv;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
$currentDir   = dirname(__DIR__);
$autoloadFile = "{$currentDir}/vendor/autoload.php";
$envFile      = "{$currentDir}/.env";

require $autoloadFile;

new Dotenv()->bootEnv($envFile);

$debug = $_SERVER['APP_DEBUG'] ?? false;

if ((bool) $debug) {
	umask(mask: 0o000);
}
