<?php

declare(strict_types=1);

namespace App\DataFixtures\Catalog;

use App\Entity\Catalog\Tournament;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Override;


class TournamentFixtures extends Fixture {
	private array $prefixes = [
		'VOS',
		'VOT',
		'VTC'
	];
	private string $schema = '';
    private array $metadatas = [];

    public function __construct()
    {
        foreach ($this->prefixes as $prefix) {
			if ($prefix === 'VOS') {
				$this->schema .= sprintf(
					'%s_%s',
					$prefix,
					'CATALOG'
				);
			} elseif ($prefix === 'VOT') {
				// VOT metadatas
				for (
					$iteration = 1;
					$iteration <= 6;
					$iteration++
				) {
					$tourneyInteration = sprintf(
						'%s%d',
						$prefix,
						$iteration
					);
					$tourneyDescription = sprintf(
						'Vietnamese Osu!taiko Tournament %d',
						$iteration
					);

					$this->metadatas[$tourneyInteration] = $tourneyDescription;
				}

				// VOT metadatas have a special iteration with float-like number
				$specialInteration = sprintf(
					'%s%s',
					$prefix,
					'5.5'
				);
				$specialDescription = sprintf(
					'Vietnamese Osu!taiko Tournament %s',
					'5.5'
				);

				$this->metadatas[$specialInteration] = $specialDescription;
			} elseif ($prefix === 'VTC') {
				// VTC metadatas
				for (
					$iteration = 1;
					$iteration <= 3;
					$iteration++
				) {
					$tourneyInteration = sprintf(
						'%s%d',
						$prefix,
						$iteration
					);
					$tourneyDescription = sprintf(
						'Vietnam\'s Taiko Colosseum %d',
						$iteration
					);

					$this->metadatas[$tourneyInteration] = $tourneyDescription;
				}
			} else {
				// Do nothing
				break;
			}
		}

        /*
		 * Give the final assoc array a sort so that the hard-coded value gets placed
		 * in the right order
         */
		ksort(
			array: $this->metadatas,
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

		$connection->executeStatement(
			sql: <<<"SQL"
			SET search_path TO {$this->schema}
			SQL
		);

		$connection->executeStatement(
			sql: <<<"SQL"
			TRUNCATE TABLE tournaments RESTART IDENTITY CASCADE
			SQL
		);

		foreach($this->metadatas as $tourneyIteration => $tourneyDescription) {
			$tournament = new Tournament();

			$tournament->setName(name: $tourneyIteration);
			$tournament->setDescription(description: $tourneyDescription);
			$tournament->setCreateOn(
				createOn: new DateTimeImmutable(
					datetime: 'now',
					timezone: new DateTimeZone(timezone: 'UTC')
				)
			);

			$manager->persist(object: $tournament);
		}

		$manager->flush();
		$manager->clear();

		$connection->executeStatement(
			sql: <<<"SQL"
			SET search_path TO public
			SQL
		);	}
}
