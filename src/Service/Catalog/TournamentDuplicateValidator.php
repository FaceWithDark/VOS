<?php

declare(strict_types=1);

namespace App\Service\Catalog;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


/// --- Type hint namespaces --- ///
use function array_key_exists;
use function is_string;
use function sprintf;
use Override;


/// --- Internal namespaces --- ///
use App\Interface\Catalog\TournamentDuplicateValidatorInterface;
use App\Repository\Catalog\TournamentRepository;


// @mago-format-ignore-end
final readonly class TournamentDuplicateValidator implements TournamentDuplicateValidatorInterface
{
	/** Initialises the instance. */
	public function __construct(
		private TournamentRepository $repository,
	) {}

	/** Validate patch. */
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

		$tournamentName = $payload['name'];

		if ($tournamentName !== null && !is_string($tournamentName)) {
			return;
		}

		$tournamentCurrentData = $this->repository->findOneBy(criteria: ['name' => $tournamentName]);

		// Detect duplicate data if any field within the incoming payload
		// matched current one
		if ($tournamentCurrentData === null) {
			return;
		}

		if ((int) $tournamentCurrentData->getId() === $id) {
			return;
		}

		throw new BadRequestHttpException(
			message: sprintf(
				'Another tournament with the name [%s] already exists.',
				$tournamentName,
			),
		);
	}

	/** Validate post. */
	#[Override]
	public function validatePost(array $payload): void
	{
		$tournamentName = $payload['name'] ?? null;

		// Let DTO validation surface the error when 'name' field is missing
		if (!is_string($tournamentName)) {
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
}
