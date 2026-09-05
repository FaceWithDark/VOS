<?php

declare(strict_types=1);

namespace App\Api\Resource\Catalog;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Catalog\Tournament as TournamentEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[ApiResource(
	shortName: 'Tourament',
	description: '**Tournament resource** operations',
	// Link this DTO to the corresponding Doctrine Entity
	stateOptions: new Options(entityClass: TournamentEntity::class)
)]
#[Map(source: TournamentEntity::class)]
final class Tournament
{
	#[Map(source: 'name')]
	public string $tournamentName;

	#[Map(source: 'description')]
	public string $tournamentDescription;
}
