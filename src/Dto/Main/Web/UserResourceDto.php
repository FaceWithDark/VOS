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
use App\Entity\Web\UserEntity;
use App\Dto\Input\Web\UserCreateDto;
use App\Dto\Input\Web\UserUpdateDto;
use App\State\Processor\Web\UserProcessor;
use App\State\Provider\Web\UserProvider;
use Symfony\Component\HttpFoundation\Response;


#[ApiResource(
	shortName: 'User API Endpoints',
	description: 'User API resource opearations.',
	routePrefix: '/v1',
	operations: [
		new GetCollection(
			uriTemplate: '/users',
			description: 'Retrieves the collection of User API resources.',
			provider: UserProvider::class,
			status: Response::HTTP_OK,
		),
		new Post(
			uriTemplate: '/users',
			description: 'Creates a User API resource.',
			input: UserCreateDto::class,
			processor: UserProcessor::class,
			status: Response::HTTP_CREATED,
		),
		new Get(
			uriTemplate: '/users/{id}',
			uriVariables: ['id'],
			requirements: ['id' => '\d+'],
			description: 'Retrieves a User API resource.',
			provider: UserProvider::class,
			status: Response::HTTP_OK,
		),
		new Patch(
			uriTemplate: '/users/{id}',
			uriVariables: ['id'],
			requirements: ['id' => '\d+'],
			description: 'Updates a User API resource',
			input: UserUpdateDto::class,
			processor: UserProcessor::class,
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
			uriTemplate: '/users/{id}',
			uriVariables: ['id'],
			requirements: ['id' => '\d+'],
			description: 'Removes a User API resource',
			processor: UserProcessor::class,
			status: Response::HTTP_NO_CONTENT,
			/*
			 * NOTE:
			 * The User resource exposes `roleId` as a scalar FK while the entity
			 * holds a {@see \App\Entity\Web\RoleEntity}. API Platform's ObjectMapper
			 * can map that forward (entity -> DTO) but not backwards (DTO -> entity),
			 * and it would otherwise attempt the reverse for DELETE. The processor
			 * only needs the route ID, hence this operation opts out of mapping.
			 */
			map: false,
		),
	],
	stateOptions: new Options(entityClass: UserEntity::class),
)]
#[Map(source: UserEntity::class)]
final class UserResourceDto
{
	public int $id;

	#[Map(source: 'roleId.id')]
	public int $roleId;

	public string $name;

	public string $avatar;

	public int $rank;

	public string $countryFlag;

	#[Map(source: 'createOn')]
	public DateTimeImmutable $timestamp;
}
