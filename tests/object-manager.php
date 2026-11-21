<?php

declare(strict_types=1);


/// --- Main namespaces --- ///
use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Dotenv\Dotenv;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

$environment = $_SERVER['APP_ENV'] ?? 'dev';
$debug = $_SERVER['APP_DEBUG'] ?? false;

$kernel = new Kernel(
	environment: is_string($environment) ? $environment : 'dev',
	debug: (bool) $debug,
);
$kernel->boot();

$entityManager = $kernel->getContainer()->get('doctrine')->getManager();

if (!$entityManager instanceof EntityManagerInterface) {
	throw new RuntimeException('Expected the Doctrine ORM entity manager.');
}

return $entityManager;
