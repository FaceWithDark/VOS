<?php

declare(strict_types=1);

namespace App;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
class Kernel extends BaseKernel
{
	use MicroKernelTrait;

	/**
	 * @return list<string> An array of allowed values for APP_ENV
	 */
	private function getAllowedEnvs(): array
	{
		return ['prod', 'dev', 'test'];
	}
}
