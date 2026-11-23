<?php

$currentDir  = dirname(__DIR__);
$preloadFile = "{$currentDir}/var/cache/prod/*.preload.php";

$preloadFiles = glob($preloadFile);

foreach ($preloadFiles === false ? [] : $preloadFiles as $file) {
	require $file;
}
