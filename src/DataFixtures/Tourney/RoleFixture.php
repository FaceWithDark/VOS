<?php

declare(strict_types=1);

namespace App\DataFixtures\Tourney;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;


/// --- Type hint namespaces --- ///
use const SORT_REGULAR;
use DateTimeImmutable;
use DateTimeZone;
use function sort;
use function sprintf;
use Override;


/// --- Internal namespaces --- ///
use App\Entity\Tourney\Role;


// @mago-format-ignore-end
class RoleFixture extends Fixture
{
	/** @var list<string> */
	private array $prefixes = [
		'VOT',
		'VTC',
	];
	/** @var list<string> */
	private array $schemas = [];
	/** @var array<string, string> */
	private array $roles = [
		'Organiser'   => 'can manage their tournament page after register with the org.',
		'Mappooler'   => 'can add/delete beatmaps from a mappool within assigned tournaments.',
		'Customiser'  => 'can add/delete their custom beatmaps in any mappool within assigned tournaments.',
		'Playtester'  => 'can perform the same level of permission as the regular user role. This role created mainly for viewing on staff page.',
		'Referee'     => 'can use our tournament management tools to take control of picked match within assigned tournaments.',
		'Streamer'    => 'can perform the same level of permission as the regular user role. This role created mainly for viewing on staff page.',
		'Commentator' => 'can perform the same level of permission as the regular user role. This role created mainly for viewing on staff page.',
		'Player'      => 'can enrol to any registered tournaments and use tools that are within the level of permission for this role.',
	];

	/** Initialises the instance. */
	public function __construct()
	{
		foreach ($this->prefixes as $prefix) {
			if ($prefix === 'VOT') {
				// VOT schemas (e.g., 'vot_vot6')
				for ($iteration = 1; $iteration <= 6; ++$iteration) {
					$this->schemas[] = sprintf(
						'%s_%s%d',
						$prefix,
						$prefix,
						$iteration,
					);
				}

				// VOT schema have a special iteration with float-like number (e.g.,
				// 'vot_vot5_5')
				$this->schemas[] = sprintf(
					'%s_%s%s',
					$prefix,
					$prefix,
					'5_5',
				);
				continue;
			}

			if ($prefix === 'VTC') {
				// VTC schemas (e.g., 'vtc_vtc3')
				for ($iteration = 1; $iteration <= 3; ++$iteration) {
					$this->schemas[] = sprintf(
						'%s_%s%d',
						$prefix,
						$prefix,
						$iteration,
					);
				}
				continue;
			}

			// Do nothing
			break;
		}

		/*
		 * Give the final array a sort so that the hard-coded value gets placed
		 * in the right order
		 */
		sort(
			array: $this->schemas,
			flags: SORT_REGULAR,
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

		foreach ($this->schemas as $tourneySchema) {
			$connection->executeStatement(
				sql: <<<SQL
					SET search_path TO {$tourneySchema}
					SQL,
			);

			$connection->executeStatement(
				sql: <<<SQL
					TRUNCATE TABLE roles RESTART IDENTITY CASCADE
					SQL,
			);

			foreach ($this->roles as $tourneyRole => $tourneyDescription) {
				$role = new Role();

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
		}

		$connection->executeStatement(
			sql: <<<SQL
				SET search_path TO public
				SQL,
		);
	}
}
