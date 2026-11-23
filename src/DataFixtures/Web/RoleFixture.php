<?php

declare(strict_types=1);

namespace App\DataFixtures\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;
use DateTimeZone;
use function sprintf;
use Override;


/// --- Internal namespaces --- ///
use App\Entity\Web\RoleEntity;


// @mago-format-ignore-end
class RoleFixture extends Fixture
{
	/** Prefix. */
	private string $prefix = 'VOS';
	/** Schema. */
	private string $schema = '';
	/** @var array<string, string> */
	private array $roles = [
		'User'  => 'can only interact with what exposed to the website.',
		'Admin' => 'can take control of the whole website both internally and externally.',
	];

	/** Initialises the instance. */
	public function __construct()
	{
		if ($this->prefix !== 'VOS') {
			// Do nothing
			return;
		}

		// VOS schemas
		$this->schema .= sprintf(
			'%s_%s',
			$this->prefix,
			'CATALOG',
		);
	}

	/** Load. */
	#[Override]
	public function load(ObjectManager $manager): void
	{
		if (!$manager instanceof EntityManagerInterface) {
			return;
		}

		$connection = $manager->getConnection();

		$connection->executeStatement(
			sql: <<<SQL
				SET search_path TO {$this->schema}
				SQL,
		);

		$connection->executeStatement(
			sql: <<<SQL
				TRUNCATE TABLE roles RESTART IDENTITY CASCADE
				SQL,
		);

		foreach ($this->roles as $tourneyRole => $tourneyDescription) {
			$role = new RoleEntity();

			$role->setName(name: $tourneyRole);
			$role->setDescription(description: $tourneyDescription);
			$role->setCreateOn(
				createOn: new DateTimeImmutable(
					datetime: 'now',
					timezone: new DateTimeZone(timezone: 'UTC'),
				),
			);

			$manager->persist(object: $role);
		}

		$manager->flush();
		$manager->clear();

		$connection->executeStatement(
			sql: <<<SQL
				SET search_path TO public
				SQL,
		);
	}
}
