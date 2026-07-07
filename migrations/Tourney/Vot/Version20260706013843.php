<?php

declare(strict_types=1);


namespace DoctrineMigrations\Tourney\Vot;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260706013843 extends AbstractMigration
{
	private string	$dbUser;
	private string	$tourneyName	= 'VOT';
	private array	$tourneySchemas = [];

	public function __construct()
	{
		$this->dbUser
			=  $_ENV['DB_USER']
			?? getenv('DB_USER')
			?: 'demo';
	}

	public function getDescription(): string
	{
		return sprintf(
			'Create iteration schemas for registered %s tournaments.',
			$this->tourneyName
		);
	}

	public function up(Schema $schema): void
	{
		$this->tourneySchemas = [
			/*
			 * NOTE:
			 * it's not possible to do a for-loop on float-like tourney
			 * iteration, hence the hard-coded assoc array here
			 */
			sprintf(
				'%s_%s%s',
				$this->tourneyName,
				$this->tourneyName,
				'5_5'
			) => sprintf(
				'%s%s iteration schema for registered %s tourney.',
				$this->tourneyName,
				'5.5',
				$this->tourneyName
			)
		];

		for (
			$iteration = 1;
			$iteration <= 6;
			$iteration++
		) {
			$schemaName	= sprintf(
				'%s_%s%d',
				$this->tourneyName,
				$this->tourneyName,
				$iteration
			);
			$schemaComment	= sprintf(
				'%s%d iteration schema for registered %s tourney.',
				$this->tourneyName,
				$iteration,
				$this->tourneyName
			);

			$this->tourneySchemas[$schemaName] = $schemaComment;
		}

		/*
		 * Give the final assoc array a sort so that the hard-coded value get
		 * placed in the right order
		 */
		ksort(
			$this->tourneySchemas,
			SORT_REGULAR
		);

		foreach ($this->tourneySchemas as $name => $comment) {
			$this->addSql(
				sprintf(
					'CREATE SCHEMA IF NOT EXISTS %s AUTHORIZATION "%s"',
					$name,
					$this->dbUser
				)
			);
			$this->addSql(
				sprintf(
					"COMMENT ON SCHEMA %s IS '%s'",
					$name,
					$comment
				)
			);
		}
	}

	public function down(Schema $schema): void
	{
		$this->tourneySchemas = [
			/*
			 * NOTE:
			 * it's not possible to do a for-loop on float-like tourney
			 * iteration, hence the hard-coded array here
			 */
			sprintf(
				'%s_%s%s',
				$this->tourneyName,
				$this->tourneyName,
				'5_5'
			)
		];

		for (
			$iteration = 1;
			$iteration <= 6;
			$iteration++
		) {
			$schemaName = sprintf(
				'%s_%s%d',
				$this->tourneyName,
				$this->tourneyName,
				$iteration
			);

			$this->tourneySchemas[] = $schemaName;
		}

		/*
		 * Give the final array a sort so that the hard-coded value get placed in
		 * the right order
		 */
		sort(
			$this->tourneySchemas,
			SORT_REGULAR
		);

		foreach ($this->tourneySchemas as $tourneySchema) {
			$this->addSql(
				sprintf(
					'DROP SCHEMA IF EXISTS %s CASCADE',
					$tourneySchema
				)
			);
		}
	}
}
