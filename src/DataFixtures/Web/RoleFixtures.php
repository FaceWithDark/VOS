<?php

declare(strict_types=1);

namespace App\DataFixtures\Web;

use App\Entity\Web\Role;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Override;


class RoleFixtures extends Fixture {
	private string $prefix = 'VOS';
    private string $schema = '';
	private array $roles = [
		'User'	=> 'can only interact with what exposed to the website.',
		'Admin'	=> 'can take control of the whole website both internally and externally.'
	];

    public function __construct()
    {
		if ($this->prefix == 'VOS') {
			// VOS schemas
			$this->schema .= sprintf(
				'%s_%s',
				$this->prefix,
				'CATALOG'
			);
		} else {
			// Do nothing
			return;
		}
    }

	#[Override]
	public function load(ObjectManager $manager): void
	{
		if (!$manager instanceof EntityManagerInterface) {
			return;
		}

		$connection = $manager->getConnection();

		$connection->executeStatement(
			sql: <<<"SQL"
			SET search_path TO {$this->schema}
			SQL
		);

		$connection->executeStatement(
			sql: <<<"SQL"
			TRUNCATE TABLE roles RESTART IDENTITY CASCADE
			SQL
		);

		foreach($this->roles as $tourneyRole => $tourneyDescription) {
			$role = new Role();

			$role->setName(name: $tourneyRole);
			$role->setDescription(description: $tourneyDescription);
			$role->setCreateOn(
				createOn: new DateTimeImmutable(
					datetime: 'now',
					timezone: new DateTimeZone(timezone: 'UTC')
				)
			);

			$manager->persist(object: $role);
		}

		$manager->flush();
		$manager->clear();

		$connection->executeStatement(
			sql: <<<"SQL"
			SET search_path TO public
			SQL
		);
	}
}
