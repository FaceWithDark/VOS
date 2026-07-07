<?php

declare(strict_types=1);


namespace DoctrineMigrations\Tourney\Vtc;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260706053601 extends AbstractMigration
{
	private string	$dbUser;
	private string	$tourneyName	= 'VTC';
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
		for (
			$iteration = 1;
			$iteration <= 3;
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
		 * NOTE:
		 * keeping this since we might have to handle similar case later on like
		 * the migration script for registered VOT tourneys
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
		for (
			$iteration = 1;
			$iteration <= 3;
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
		 * NOTE:
		 * keeping this since we might have to handle similar case later on like
		 * the migration script for registered VOT tourneys
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
