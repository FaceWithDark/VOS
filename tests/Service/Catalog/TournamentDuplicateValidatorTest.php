<?php

declare(strict_types=1);

namespace App\Tests\Service\Catalog;


/// --- Main namespaces --- ///
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;


/// --- Type hint namespaces --- ///
use Override;


/// --- Internal namespaces --- ///
use App\Entity\Catalog\TournamentEntity;
use App\Repository\Catalog\TournamentRepository;
use App\Service\Catalog\TournamentDuplicateValidator;


#[CoversClass(className: TournamentDuplicateValidator::class)]
class TournamentDuplicateValidatorTest extends TestCase
{
	private TournamentRepository&MockObject $repository;
	private TournamentDuplicateValidator	$duplcateValidator;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository			= $this->createMock(type: TournamentRepository::class);
		$this->duplcateValidator	= new TournamentDuplicateValidator(repository: $this->repository);
	}


	/**
	 * validatePost() - 409 Conflicts
	 */


	#[Test]
    public function testMissingNameFieldOnPost(): void
    {
		$testPayload = ['description' => 'Vietnamese Osu!taiko Tournament 88 (special edition).'];

		$this
			->repository
			->expects(self::never())
			->method('findOneBy');

		$this
			->duplcateValidator
			->validatePost(payload: $testPayload);

		// No exception found means a valid pass
        $this->addToAssertionCount(count: 1);
    }

	#[Test]
	public function testNullNameFieldOnPost(): void
	{
		$testPayload = ['name' => null];

		$this
			->repository
			->expects(self::never())
			->method('findOneBy');

		$this
			->duplcateValidator
			->validatePost(payload: $testPayload);

        // No exception found means a valid pass
        $this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testPassedNameFieldOnPost(): void
	{
		$testPayload = ['name' => 'VOT88'];

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn(null);

		$this
			->duplcateValidator
			->validatePost(payload: $testPayload);

		// No exception found means a valid pass
        $this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testMatchingNameFieldOnPost(): void
	{
		$testPayload			= ['name' => 'VOT6'];
		$tournamentEntity		= new TournamentEntity();
		$tournamentCurrentData	= $tournamentEntity->setName(name: $testPayload['name']);

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn($tournamentCurrentData);

		$this->expectException(exception: ConflictHttpException::class);
		$this->expectExceptionMessage(message: "A tournament with the name [{$testPayload['name']}] already exists.");

		$this
			->duplcateValidator
			->validatePost(payload: $testPayload);
	}

	#[Test]
	public function testOptionalDescriptionFieldOnPost(): void
	{
		$testPayload = ['name' => 'VOT88'];

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn(null);

		$this
			->duplcateValidator
			->validatePost(
				payload: array_merge(
					$testPayload,
					['description' => 'Vietnamese Osu!taiko Tournament 88 (special edition).'],
				),
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}


	/**
	 * validatePatch() - 400 Bad Request
	 */


	#[Test]
	public function testMissingNameFieldOnPatch(): void
	{
		$testPayload = ['description' => 'Vietnamese Osu!taiko Tournament 88 (special edition).'];

		$this
			->repository
			->expects(self::never())
			->method('findOneBy');

		$this
			->duplcateValidator
			->validatePatch(
				payload: $testPayload,
				id: 7,
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testNullNameFieldOnPatch(): void
	{
		$testPayload = ['name' => null];

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn(null);

		$this
			->duplcateValidator
			->validatePatch(
				payload: $testPayload,
				id: 7,
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testPassedNameFieldOnPatch(): void
	{
		$testPayload = ['name' => 'VOT88'];

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn(null);

		$this
			->duplcateValidator
			->validatePatch(
				payload: $testPayload,
				id: 7,
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testSameEntityMatchingNameFieldOnPatch(): void
	{
		$testPayload			= ['name' => 'VOT6'];
		$tournamentEntity		= new TournamentEntity();
		$tournamentCurrentData	= $tournamentEntity->setName(name: $testPayload['name'])->setId(id: 7);

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn($tournamentCurrentData);

		$this
			->duplcateValidator
			->validatePatch(
				payload: $testPayload,
				id: 7,
			);

		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testDifferentEntityMatchingNameFieldOnPatch(): void
	{
		$testPayload = ['name' => 'VOT6'];
		$tournamentEntity = new TournamentEntity();
		$tournamentCurrentData
			= $tournamentEntity
			->setName(name: $testPayload['name'])
			->setId(id: 8);

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn($tournamentCurrentData);

		$this->expectException(exception: BadRequestHttpException::class);
		$this->expectExceptionMessage(message: "Another tournament with the name [{$testPayload['name']}] already exists.");

		$this
			->duplcateValidator
			->validatePatch(
				payload: $testPayload,
				id: 7,
			);
	}

	#[Test]
	public function testOptionalDescriptionFieldOnPatch(): void
	{
		$testPayload = ['name' => 'VOT88'];

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn(null);

		$this
			->duplcateValidator
			->validatePatch(
				payload: array_merge(
					$testPayload,
					['description' => 'Vietnamese Osu!taiko Tournament 88 (special edition).'],
				),
				id: 7,
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}
}
