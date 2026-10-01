<?php

declare(strict_types=1);

namespace App\Tests\Service\Web;


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
use App\Entity\Web\RoleEntity;
use App\Repository\Web\RoleRepository;
use App\Service\Web\RoleDuplicateValidator;


#[CoversClass(className: RoleDuplicateValidator::class)]
class RoleDuplicateValidatorTest extends TestCase
{
	private RoleRepository&MockObject	$repository;
	private RoleDuplicateValidator		$duplcateValidator;

	#[Override]
	protected function setUp(): void
	{
		parent::setUp();

		// Fresh mocks per test (Symfony/PHPUnit best practice for isolation)
		$this->repository			= $this->createMock(type: RoleRepository::class);
		$this->duplcateValidator	= new RoleDuplicateValidator(repository: $this->repository);
	}


	/**
	 * validatePost() - 409 Conflicts
	 */


	#[Test]
    public function testMissingNameFieldOnPost(): void
    {
		$testPayload = ['description' => 'double the pay, double the deal baby. That is what high risk high reward about.'];

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
		$testPayload = ['name' => 'Gambler'];

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
		$testPayload		= ['name' => 'Admin'];
		$roleEntity			= new RoleEntity();
		$roleCurrentData	= $roleEntity->setName(name: $testPayload['name']);

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn($roleCurrentData);

		$this->expectException(exception: ConflictHttpException::class);
		$this->expectExceptionMessage(message: "A role with the name [{$testPayload['name']}] already exists.");

		$this
			->duplcateValidator
			->validatePost(payload: $testPayload);
	}

	#[Test]
	public function testOptionalDescriptionFieldOnPost(): void
	{
		$testPayload = ['name' => 'Gambler'];

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
					['description' => 'double the pay, double the deal baby. That is what high risk high reward about.'],
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
		$testPayload = ['description' => 'double the pay, double the deal baby. That is what high risk high reward about.'];

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
		$testPayload = ['name' => 'Gambler'];

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
		$testPayload		= ['name' => 'Admin'];
		$roleEntity			= new RoleEntity();
		$roleCurrentData	= $roleEntity->setName(name: $testPayload['name'])->setId(id: 2);

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn($roleCurrentData);

		$this
			->duplcateValidator
			->validatePatch(
				payload: $testPayload,
				id: 2,
			);

		$this->addToAssertionCount(count: 1);
	}

	#[Test]
	public function testDifferentEntityMatchingNameFieldOnPatch(): void
	{
		$testPayload = ['name' => 'Admin'];
		$roleEntity = new RoleEntity();
		$roleCurrentData
			= $roleEntity
			->setName(name: $testPayload['name'])
			->setId(id: 1);

		$this
			->repository
			->expects(self::once())
			->method('findOneBy')
			->with($testPayload)
			->willReturn($roleCurrentData);

		$this->expectException(exception: BadRequestHttpException::class);
		$this->expectExceptionMessage(message: "Another role with the name [{$testPayload['name']}] already exists.");

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
		$testPayload = ['name' => 'Gambler'];

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
					['description' => 'double the pay, double the deal baby. That is what high risk high reward about.'],
				),
				id: 7,
			);

		// No exception found means a valid pass
		$this->addToAssertionCount(count: 1);
	}
}
