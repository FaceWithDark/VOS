<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Catalog;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Entity\Abstract\TournamentAbstract;
use App\Entity\Catalog\TournamentEntity;
use App\Repository\Catalog\TournamentRepository;
use App\Service\Catalog\TournamentDuplicateValidator;


// @mago-format-ignore-end
/**
 * NOTE:
 *
 * The validator is a thin policy layer around {@see TournamentRepository}: it
 * decides which HTTP error a repeated tournament name maps to. The repository
 * is mocked so each test pins exactly one policy branch.
 */
#[CoversClass(TournamentDuplicateValidator::class)]
#[UsesClass(TournamentEntity::class)]
#[UsesClass(TournamentAbstract::class)]
class TournamentDuplicateValidatorTest extends TestCase
{
	/** Repository. */
	protected TournamentRepository&MockObject $repository;

	/** Duplicate validator. */
	protected TournamentDuplicateValidator $duplicateValidator;

	/**
	 * Covers: test patch checks duplicate by name only.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPatchChecksDuplicateByNameOnly(): void
	{
		$this->repository->expects(self::once())->method('findOneBy')->with(['name' => 'VOT88'])->willReturn(null);

		$this->sealRepository();

		$this->duplicateValidator->validatePatch(
			payload: [
				'name'        => 'VOT88',
				'description' => 'Vietnamese Osu!taiko Tournament 88 (special edition).',
			],
			id: 7,
		);
	}

	/**
	 * Covers: test patch with another entity name throws bad request.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPatchWithAnotherEntityNameThrowsBadRequest(): void
	{
		$current = $this->mockTournamentEntity(
			id: 8,
			name: 'VOT6',
		);

		$this->repository->expects(self::once())->method('findOneBy')->with(['name' => 'VOT6'])->willReturn($current);

		$this->expectException(BadRequestHttpException::class);

		$this->expectExceptionMessageIs('Another tournament with the name [VOT6] already exists.');

		$this->sealRepository();

		$this->duplicateValidator->validatePatch(
			payload: ['name' => 'VOT6'],
			id: 7,
		);
	}

	/**
	 * Covers: test patch with null name looks up null and passes.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPatchWithNullNameLooksUpNullAndPasses(): void
	{
		// `array_key_exists` treats an explicit NULL as a real update, so the
		// NULL is looked up and simply not found.
		$this->repository->expects(self::once())->method('findOneBy')->with(['name' => null])->willReturn(null);

		$this->sealRepository();

		$this->duplicateValidator->validatePatch(
			payload: ['name' => null],
			id: 7,
		);
	}

	/**
	 * Covers: test patch without name skips lookup.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testPatchWithoutNameSkipsLookup(): void
	{
		// A partial update that does not touch 'name' must not query at all
		$this->repository->expects(self::never())->method('findOneBy');

		$this->sealRepository();

		$this->duplicateValidator->validatePatch(
			payload: ['description' => 'Vietnamese Osu!taiko Tournament 88 (special edition).'],
			id: 7,
		);
	}

	/**
	 * Covers: test patch with own name passes.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testPatchWithOwnNamePasses(): void
	{
		$current = $this->mockTournamentEntity(
			id: 7,
			name: 'VOT6',
		);

		$this->repository->expects(self::once())->method('findOneBy')->with(['name' => 'VOT6'])->willReturn($current);

		// Re-sending the current value must NOT be treated as a duplicate
		$this->sealRepository();

		$this->duplicateValidator->validatePatch(
			payload: ['name' => 'VOT6'],
			id: 7,
		);
	}

	/**
	 * Covers: test patch with unique name passes.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPatchWithUniqueNamePasses(): void
	{
		$this->repository->expects(self::once())->method('findOneBy')->with(['name' => 'VOT88'])->willReturn(null);

		$this->sealRepository();

		$this->duplicateValidator->validatePatch(
			payload: ['name' => 'VOT88'],
			id: 7,
		);
	}

	/**
	 * Covers: test post checks duplicate by name only.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPostChecksDuplicateByNameOnly(): void
	{
		// The sibling 'description' field must never leak into the criteria
		$this->repository->expects(self::once())->method('findOneBy')->with(['name' => 'VOT88'])->willReturn(null);

		$this->sealRepository();

		$this->duplicateValidator->validatePost(payload: [
			'name'        => 'VOT88',
			'description' => 'Vietnamese Osu!taiko Tournament 88 (special edition).',
		]);
	}

	/**
	 * Covers: test post with duplicate name throws conflict.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPostWithDuplicateNameThrowsConflict(): void
	{
		$current = $this->mockTournamentEntity(name: 'VOT6');

		$this->repository->expects(self::once())->method('findOneBy')->with(['name' => 'VOT6'])->willReturn($current);

		$this->expectException(ConflictHttpException::class);

		$this->expectExceptionMessageIs('A tournament with the name [VOT6] already exists.');

		$this->sealRepository();

		$this->duplicateValidator->validatePost(payload: ['name' => 'VOT6']);
	}

	/**
	 * validatePost() - 409 Conflict
	 */
	/**
	 * validatePatch() - 400 Bad Request
	 */

	/**
	 * Covers: test post with missing name skips lookup.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testPostWithMissingNameSkipsLookup(): void
	{
		// 'name' absent: let the DTO NotBlank constraint surface the error
		$this->repository->expects(self::never())->method('findOneBy');

		$this->sealRepository();

		$this->duplicateValidator->validatePost(payload: [
			'description' => 'Vietnamese Osu!taiko Tournament 88 (special edition).',
		]);
	}

	/**
	 * Covers: test post with null name skips lookup.
	 * @mago-expect lint:literal-named-argument
	 */
	#[Test]
	public function testPostWithNullNameSkipsLookup(): void
	{
		// `?? null` treats an explicit NULL the same as a missing field
		$this->repository->expects(self::never())->method('findOneBy');

		$this->sealRepository();

		$this->duplicateValidator->validatePost(payload: ['name' => null]);
	}

	/**
	 * Covers: test post with unique name passes.
	 * @mago-expect lint:literal-named-argument(2)
	 */
	#[Test]
	public function testPostWithUniqueNamePasses(): void
	{
		$this->repository->expects(self::once())->method('findOneBy')->with(['name' => 'VOT88'])->willReturn(null);

		$this->sealRepository();

		$this->duplicateValidator->validatePost(payload: ['name' => 'VOT88']);
	}

	/** Covers: mock tournament entity. */
	protected function mockTournamentEntity(
		int    $id = 7,
		string $name = 'VOT6',
	): TournamentEntity {
		return new TournamentEntity()
			->setId(id: $id)
			->setName(name: $name);
	}

	/**
	 * Seal repository.
	 * @mago-expect lint:literal-named-argument
	 */
	protected function sealRepository(): void
	{
		$this->repository->method('findOneBy')->seal();
	}

	/** Prepares the test fixtures. */
	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository = $this->createMock(TournamentRepository::class);

		$this->duplicateValidator = new TournamentDuplicateValidator(repository: $this->repository);
	}
}
