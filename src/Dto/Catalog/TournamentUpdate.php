<?php

declare(strict_types=1);

namespace App\Dto\Catalog;


/// --- Main namespaces --- ///
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraint as Assert;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Catalog\Tournament as TournamentEntity;


#[Map(target: TournamentEntity::class)]
final class TournamentUpdate
{
	#[Assert\NotBlank(message: "'tournamentName' value should not be blank!")]
	public string $name;

	#[Map(target: 'description')]
	public string $desc;
}
