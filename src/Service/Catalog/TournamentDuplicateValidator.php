<?php

declare(strict_types=1);

namespace App\Service\Catalog;


/// --- Main namespaces --- ///
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Interface\Catalog\TournamentDuplicateValidatorInterface;
use App\Repository\Catalog\TournamentRepository;


final readonly class TournamentDuplicateValidator implements TournamentDuplicateValidatorInterface
{
	public function __construct(private TournamentRepository $repository) {}

	#[Override]
	public function validatePost(array $payload): void
	{
		$tournamentName = $payload['name'] ?? null;

		// Let DTO validation surface the error when 'name' field is missing
		if ($tournamentName === null) {
			return;
		}

		$tournamentCurrentNameValue = $this->repository->findOneBy(criteria: ['name' => $tournamentName]);

		if ($tournamentCurrentNameValue !== null) {
			throw new ConflictHttpException(
				message: sprintf(
					'A tournament with the name [%s] already exists.',
					$tournamentName,
				),
			);
		}
	}

	#[Override]
	public function validatePatch(array $payload, int $id): void
	{
		// Only validate 'name' field when the client actually sent it (partial update)
		if (!array_key_exists(
			key: 'name',
			array: $payload,
		)) {
			return;
		}

		$tournamentCurrentData = $this->repository->findOneBy(criteria: [['name'] => $payload['name']]);

		// Detect duplicate data if any field within the incoming payload
		// matched current one
		if ($tournamentCurrentData === null || (int) $tournamentCurrentData->getId() === $id) {
			return;
		}

		throw new BadRequestException(
			message: sprintf(
				'Another tournament with the name [%s] already exists.',
				$payload['name'],
			),
		);
	}
}
