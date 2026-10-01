<?php

declare(strict_types=1);

namespace App\Dto\Main\Web;


/// --- Main namespaces --- ///
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;


/// --- Internal namespaces --- ///
use App\Entity\Web\RoleEntity;
use App\Dto\Input\Web\RoleCreateDto;
use App\Dto\Input\Web\RoleUpdateDto;
use App\State\Processor\Web\RoleProcessor;
use App\State\Provider\Web\RoleProvider;
use Symfony\Component\HttpFoundation\Response;


#[ApiResource(
	shortName: 'Role API Endpoints',
	description: 'Role API resource opearations.',
	routePrefix: '/v1',
	operations: [
		new GetCollection(
			uriTemplate: '/roles',
			description: 'Retrieves the collection of Role API resources.',
			provider: RoleProvider::class,
			status: Response::HTTP_OK,
		),
		new Post(
			uriTemplate: '/roles',
			description: 'Creates a Role API resource.',
			input: RoleCreateDto::class,
			processor: RoleProcessor::class,
			status: Response::HTTP_CREATED,
		),
		new Get(
			uriTemplate: '/roles/{id}',
			uriVariables: ['id'],
			requirements: ['id' => '\d+'],
			description: 'Retrieves a Role API resource.',
			provider: RoleProvider::class,
			status: Response::HTTP_OK,
		),
		new Patch(
			uriTemplate: '/roles/{id}',
			uriVariables: ['id'],
			requirements: ['id' => '\d+'],
			description: 'Updates a Role API resource',
			input: RoleUpdateDto::class,
			processor: RoleProcessor::class,
			status: Response::HTTP_OK,
			/*
			 * NOTE:
			 * PATCH request behaves a little different than POST so we've to explicitly
			 * set these options along with global one to ensure that the validation
			 * working as intended.
			 */
			denormalizationContext: [
				AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES				=> false,
				DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS	=> true,
			]
		),
		new Delete(
			uriTemplate: '/roles/{id}',
			uriVariables: ['id'],
			requirements: ['id' => '\d+'],
			description: 'Removes a Role API resource',
			processor: RoleProcessor::class,
			status: Response::HTTP_NO_CONTENT,
		),
	],
	stateOptions: new Options(entityClass: RoleEntity::class),
)]
#[Map(source: RoleEntity::class)]
final class RoleResourceDto
{
	public int $id;

	public string $name;

	public ?string $description = null;

	#[Map(source: 'createOn')]
	public DateTimeImmutable $timestamp;
}
