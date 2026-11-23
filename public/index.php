<?php

// @mago-format-ignore-start


/// --- Main namespaces --- ///


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Kernel;


// @mago-format-ignore-end
$currentDir  = dirname(__DIR__);
$runtimeFile = "{$currentDir}/vendor/autoload_runtime.php";

require_once $runtimeFile;

return static fn(array $context): Kernel => new Kernel(
	environment: $context['APP_ENV'],
	debug: (bool) $context['APP_DEBUG'],
);
