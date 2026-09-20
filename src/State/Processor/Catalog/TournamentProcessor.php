<?php

declare(strict_types=1);

namespace App\State\Processor\Catalog;


/// --- Main namespaces --- ///
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Override;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;


/// --- Internal namespaces --- ///
use App\Dto\Input\Catalog\TournamentCreateDto;
use App\Dto\Input\Catalog\TournamentUpdateDto;
use App\Dto\Main\Catalog\TournamentResourceDto;
use App\Entity\Catalog\TournamentEntity;
use App\Interface\Catalog\TournamentDuplicateValidatorInterface;
use App\Repository\Catalog\TournamentRepository;


/**
 * @implements ProcessorInterface<TournamentCreateDto|TournamentUpdateDto|mixed, TournamentResourceDto|null>
 */
final readonly class TournamentProcessor implements ProcessorInterface
{
	public function __construct(
		private TournamentRepository					$repository,
		private ObjectMapperInterface					$mapper,
		private RequestStack							$requestStack,
		private TournamentDuplicateValidatorInterface	$duplicateValidator,
	) {}

	private function getDecodedPayload(): array
	{
		$request = $this->requestStack->getCurrentRequest();
		$decoded
			= $request
			? json_decode(
				json: $request->getContent(),
				associative: true,
			)
			: null;

		return is_array(value: $decoded) ? $decoded : [];
	}

	#[Override]
	public function process(
		mixed		$data,
		Operation	$operation,
		array		$payload	= [],
		array		$context	= [],
	): ?TournamentResourceDto
	{
		return match (true) {
			$data instanceof TournamentCreateDto	=> $this->handlePost(dto: $data),
			$data instanceof TournamentUpdateDto	=> $this->handlePatch(dto: $data, payload: $payload),
			$operation instanceof Delete			=> $this->handleDelete(payload: $payload),
			default									=> throw new InvalidArgumentException('Unsupported opearation or input DTO type.'),
		};
	}


	private function handlePost(TournamentCreateDto $dto): TournamentResourceDto
	{
		$this->duplicateValidator->validatePost(payload: $this->getDecodedPayload());

		$tournamentEntity = new TournamentEntity();

		$tournamentEntity->setName(name: $dto->name);
		$tournamentEntity->setDescription(description: $dto->description);
		$tournamentEntity->setCreateOn(
			createOn: new DateTimeImmutable(
				datetime: 'now',
				timezone: new DateTimeZone(timezone: 'UTC'),
			)
		);

		$this->repository->save(
			entity: $tournamentEntity,
			flush: true
		);

		return $this->mapper->map(
			source: $tournamentEntity,
			target: TournamentResourceDto::class,
		);
	}

	private function handlePatch(
		TournamentUpdateDto $dto,
		array $payload,
	): TournamentResourceDto
	{
		$tournamentId = (int) ($payload['id' ?? 0]);
		$tournamentEntity = $this->repository->find(id: $tournamentId);

		if (!$tournamentEntity) {
			throw new NotFoundHttpException(
				message: sprintf(
					'Tournament with ID [%d] not found.',
					$tournamentId,
				),
			);
		}

		$tournamentDecodedPayload = $this->getDecodedPayload();

		// 400 if another entity already uses the same 'name' value
		$this->duplicateValidator->validatePatch(
			payload: $tournamentDecodedPayload,
			id: $tournamentId
		);

		// Apply the PATCH request since we allowed 'description' field value to be NULL
		if (array_key_exists(
			key: 'name',
			array: $tournamentDecodedPayload,
		)) {
			$tournamentEntity->setName(name: $dto->name);
		}

		if (array_key_exists(
			key: 'description',
			array: $tournamentDecodedPayload,
		)) {
			$tournamentEntity->setDescription(description: $dto->description);
		}

		$this->repository->save(
			entity: $tournamentEntity,
			flush: true,
		);

		return $this->mapper->map(
			source: $tournamentEntity,
			target: TournamentResourceDto::class,
		);
	}

	private function handleDelete(array $payload): null
	{
		$tournamentId = ((int) $payload['id']) ?? null;
		$tournamentEntity = $this->repository->find(id: $tournamentId);

		if (!$tournamentEntity) {
			throw new NotFoundHttpException(
				sprintf(
					'Tournament with ID [%d] not found.',
					(int) $tournamentId,
				)
			);
		}

		$this->repository->remove(
			entity: $tournamentEntity,
			flush: true,
		);

		return null;
	}
}
