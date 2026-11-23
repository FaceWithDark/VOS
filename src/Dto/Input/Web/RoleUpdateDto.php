<?php

declare(strict_types=1);

namespace App\Dto\Input\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Symfony\Component\Validator\Constraints as Assert;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
final class RoleUpdateDto
{
	/** Name. */
	public ?string $name = null;

	/** Description. */
	#[Assert\Type(['string', 'null'])]
	public ?string $description = null;
}
