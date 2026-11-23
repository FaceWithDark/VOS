<?php

declare(strict_types=1);

namespace App\Service\Catalog;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Symfony\Component\HttpFoundation\RequestStack;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;
use DateTimeZone;
use function array_key_exists;
use function is_array;
use function json_decode;


/// --- Internal namespaces --- ///
use App\Dto\Input\Catalog\TournamentCreateDto;
use App\Dto\Input\Catalog\TournamentUpdateDto;
use App\Entity\Catalog\TournamentEntity;
use App\Interface\Catalog\TournamentDuplicateValidatorInterface;
use App\Interface\Catalog\TournamentEmptyPayloadValidatorInterface;


// @mago-format-ignore-end
/**
 * Reads, validates, and applies the tournament request payload, keeping the
 * processor itself focused on persistence and DTO mapping.
 */
final readonly class TournamentPayloadHandler
{
	/** Initialises the instance. */
	public function __construct(
		private RequestStack                             $requestStack,
		private TournamentDuplicateValidatorInterface    $duplicateValidator,
		private TournamentEmptyPayloadValidatorInterface $emptyPayloadValidator,
	) {}

	/** Applies the create DTO to the entity. */
	public function applyCreate(TournamentEntity $entity, TournamentCreateDto $dto): void
	{
		$entity->setName(name: $dto->name);
		$entity->setDescription(description: $dto->description);
		$entity->setCreateOn(
			createOn: new DateTimeImmutable(
				datetime: 'now',
				timezone: new DateTimeZone(timezone: 'UTC'),
			),
		);
	}

	/**
	 * @param array<array-key, mixed> $payload
	 */
	public function applyPatch(TournamentEntity $entity, TournamentUpdateDto $dto, array $payload): void
	{
		if (
			array_key_exists(
				key: 'name',
				array: $payload,
			)
			&& $dto->name !== null
		) {
			$entity->setName(name: $dto->name);
		}

		if (array_key_exists(
			key: 'description',
			array: $payload,
		)) {
			$entity->setDescription(description: $dto->description);
		}
	}

	/**
	 * @return array<array-key, mixed>
	 */
	public function readPayload(): array
	{
		$request = $this->requestStack->getCurrentRequest();

		if ($request === null) {
			return [];
		}

		$decoded = json_decode(
			json: $request->getContent(),
			associative: true,
		);

		if (is_array(value: $decoded)) {
			return $decoded;
		}

		return [];
	}

	/**
	 * @param array<array-key, mixed> $payload
	 */
	public function validatePatch(array $payload, int $id): void
	{
		$this->emptyPayloadValidator->validatePatch(payload: $payload);
		$this->duplicateValidator->validatePatch(
			payload: $payload,
			id: $id,
		);
	}

	/**
	 * @param array<array-key, mixed> $payload
	 */
	public function validatePost(array $payload): void
	{
		$this->duplicateValidator->validatePost(payload: $payload);
	}
}
