<?php

declare(strict_types=1);

namespace App\Dto\Input\Web;


/// --- Main namespaces --- ///
use Symfony\Component\Validator\Constraints as Assert;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


final class RoleUpdateDto
{
	public ?string $name = null;

	#[Assert\Type(['string', 'null'])]
	public ?string $description = null;
}
