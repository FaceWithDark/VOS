<?php

declare(strict_types=1);

namespace App\Dto\Input\Catalog;


/// --- Main namespaces --- ///
use Symfony\Component\Validator\Constraint as Assert;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


final class TournamentUpdateDto
{
	public ?string $name = null;

	#[Assert\Type(['string', 'null'])]
	public ?string $description = null;
}
