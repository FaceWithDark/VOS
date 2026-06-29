<?php

declare(strict_types=1);


namespace App\Twig\Components\Navigation;


use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;


#[AsLiveComponent]
final class Sidebar extends Base
{
	use DefaultActionTrait;
}
