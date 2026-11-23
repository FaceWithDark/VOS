<?php

declare(strict_types=1);

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Dotenv\Dotenv;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Kernel;


// @mago-format-ignore-end
$currentDir   = dirname(__DIR__);
$autoloadFile = "{$currentDir}/vendor/autoload.php";
$envFile      = "{$currentDir}/.env";

require $autoloadFile;

new Dotenv()->bootEnv($envFile);

$environment = $_SERVER['APP_ENV'] ?? 'dev';
$debug       = $_SERVER['APP_DEBUG'] ?? false;

$kernel = new Kernel(
	environment: is_string($environment) ? $environment : 'dev',
	debug: (bool) $debug,
);
$kernel->boot();

$entityManager = $kernel->getContainer()->get(id: 'doctrine')->getManager();

if (!$entityManager instanceof EntityManagerInterface) {
	throw new RuntimeException(message: 'Expected the Doctrine ORM entity manager.');
}

return $entityManager;
