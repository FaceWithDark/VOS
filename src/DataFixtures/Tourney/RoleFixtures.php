<?php

declare(strict_types=1);

namespace App\DataFixtures\Tourney;

use App\Entity\Tourney\Role;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Override;


class RoleFixtures extends Fixture {
	private array $prefixes = [
		'VOT',
		'VTC'
	];
    private array $schemas = [];
	private array $roles = [
		'Organiser', 'Mappooler', 'Customiser',
		'Playtester', 'Referee', 'Streamer',
		'Commentator', 'Player'
	];

    public function __construct()
    {
        foreach ($this->prefixes as $prefix) {
			if ($prefix == 'VOT') {
				// VOT schemas (e.g., 'vot_vot6')
				for (
					$iteration = 1;
					$iteration <= 6;
					$iteration++
				) {
					$this->schemas[] = sprintf(
						'%s_%s%d',
						$prefix,
						$prefix,
						$iteration
					);
				}

				// VOT schema have a special iteration with float-like number (e.g.,
				// 'vot_vot5_5')
				$this->schemas[] = sprintf(
					'%s_%s%s',
					$prefix,
					$prefix,
					'5_5'
				);
			} elseif ($prefix == 'VTC') {
				// VTC schemas (e.g., 'vtc_vtc3')
				for (
					$iteration = 1;
					$iteration <= 3;
					$iteration++
				) {
					$this->schemas[] = sprintf(
						'%s_%s%d',
						$prefix,
						$prefix,
						$iteration
					);
				}
			} else {
				// Do nothing
				break;
			}
		}

        /*
		 * Give the final array a sort so that the hard-coded value gets placed
		 * in the right order
         */
		sort(
			array: $this->schemas,
			flags: SORT_REGULAR
		);
    }

	#[Override]
	public function load(ObjectManager $manager): void
	{
		if (!$manager instanceof EntityManagerInterface) {
			return;
		}

		$connection = $manager->getConnection();

		foreach($this->schemas as $tourneySchema) {
			$connection->executeStatement(
				sql: <<<"SQL"
				SET search_path TO {$tourneySchema}
				SQL
			);

			$connection->executeStatement(
				sql: <<<"SQL"
				TRUNCATE TABLE roles RESTART IDENTITY CASCADE
				SQL
			);

			foreach($this->roles as $tourneyRole) {
				$role = new Role();

				$role->setName(name: $tourneyRole);
				$role->setDescription(description: null);
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
		}

		$connection->executeStatement(
			sql: <<<"SQL"
			SET search_path TO public
			SQL
		);
	}
}
