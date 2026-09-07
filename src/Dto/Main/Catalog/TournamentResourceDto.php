<?php

declare(strict_types=1);

namespace App\Dto\Main\Catalog;


/// --- Main namespaces --- ///
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Symfony\Component\ObjectMapper\Attribute\Map;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;


/// --- Internal namespaces --- ///
use App\Entity\Catalog\TournamentEntity;
use App\Dto\Input\Catalog\TournamentCreateDto;
use App\Dto\Input\Catalog\TournamentUpdateDto;
use App\State\Processor\Catalog\TournamentProcessor;
use App\State\Provider\Catalog\TournamentProvider;
use Symfony\Component\HttpFoundation\Response;


#[ApiResource(
	shortName: 'Tournament API Endpoints',
	description: 'Tournament API resource opearations.',
	routePrefix: '/v1',
	operations: [
		new GetCollection(
			uriTemplate: '/tournaments',
			description: 'Retrieves the collection of Tournament API resources.',
			provider: TournamentProvider::class,
			status: Response::HTTP_OK,
		),
		new Post(
			uriTemplate: '/tournaments',
			description: 'Creates a Tournament API resource.',
			input: TournamentCreateDto::class,
			processor: TournamentProcessor::class,
			status: Response::HTTP_CREATED,
		),
		new Get(
			uriTemplate: '/tournaments/{id}',
			uriVariables: ['id'],
			requirements: ['id' => '\d+'],
			description: 'Retrieves a Tournament API resource.',
			provider: TournamentProvider::class,
			status: Response::HTTP_OK,
		),
		new Patch(
			uriTemplate: '/tournaments/{id}',
			uriVariables: ['id'],
			requirements: ['id' => '\d+'],
			description: 'Updates a Tournament API resource',
			input: TournamentUpdateDto::class,
			processor: TournamentProcessor::class,
			status: Response::HTTP_OK,
		),
		new Delete(
			uriTemplate: '/tournaments/{id}',
			uriVariables: ['id'],
			requirements: ['id' => '\d+'],
			description: 'Removes a Tournament API resource',
			processor: TournamentProcessor::class,
			status: Response::HTTP_NO_CONTENT,
		),
	],
	stateOptions: new Options(entityClass: TournamentEntity::class),
)]
#[Map(source: TournamentEntity::class)]
final class TournamentResourceDto
{
	public int $id;

	public string $name;

	public ?string $description = null;

	#[Map(source: 'createOn')]
	public DateTimeImmutable $timestamp;
}
