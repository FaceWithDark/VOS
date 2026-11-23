<?php

declare(strict_types=1);

namespace App\Twig\Components\Navigation;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
#[AsLiveComponent]
final class Navbar extends Base
{
	use DefaultActionTrait;
}
