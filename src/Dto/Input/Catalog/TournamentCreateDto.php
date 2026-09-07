<?php

declare(strict_types=1);

namespace App\Dto\Input\Catalog;


/// --- Main namespaces --- ///
use Symfony\Component\Validator\Constraint as Assert;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


final class TournamentCreateDto
{
	#[Assert\NotBlank]
	public string $name;

	public ?string $description = null;
}
