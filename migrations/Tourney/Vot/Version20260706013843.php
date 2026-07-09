<?php

declare(strict_types=1);


namespace DoctrineMigrations\Tourney\Vot;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260706013843 extends AbstractMigration
{
	private string	$dbUser;
	private string	$name		= 'VOT';
	private array	$schemas	= [];
	private string	$statement	= '';

	public function __construct()
	{
		$this->dbUser
			=  $_ENV['DB_USER']
			?? getenv('DB_USER')
			?: 'demo';

		$this->schemas = [
			/*
			 * NOTE:
			 * it's not possible to do a for-loop on float-like tourney
			 * iteration, hence the hard-coded assoc array here
			 */

			// Key is for schemas name
			sprintf(
				'%s_%s%s',
				$this->name,
				$this->name,
				'5_5'
			) =>
			// Value is for schemas comment
			sprintf(
				'%s%s iteration schema for registered %s tourney.',
				$this->name,
				'5.5',
				$this->name
			)
		];

		for (
			$iteration = 1;
			$iteration <= 6;
			$iteration++
		) {
			$schemaName = sprintf(
				'%s_%s%d',
				$this->name,
				$this->name,
				$iteration
			);

			$schemaComment = sprintf(
				'%s%d iteration schema for registered %s tourney.',
				$this->name,
				$iteration,
				$this->name
			);

			$this->schemas[$schemaName] = $schemaComment;
		}

		/*
		 * Give the final assoc array a sort so that the hard-coded value get
		 * placed in the right order
		 */
		ksort(
			$this->schemas,
			SORT_REGULAR
		);
	}

	public function getDescription(): string
	{
		return sprintf(
			'Create iteration schemas for registered %s tournaments.',
			$this->name
		);
	}

	public function up(Schema $schema): void
	{
		// This schema need to be created first
		foreach ($this->schemas as $name => $comment) {
			$this->statement =
				<<<"SQL"
				CREATE SCHEMA IF NOT EXISTS
					{$name}
				AUTHORIZATION
					"{$this->dbUser}"
				SQL;

			$this->addSql(sql: $this->statement);
		}


		// Then we can add comment on the schema since they're existed now
		foreach ($this->schemas as $name => $comment) {
			$this->statement =
				<<<"SQL"
				COMMENT ON SCHEMA
					{$name}
				IS
					'{$comment}'
				SQL;

			$this->addSql(sql: $this->statement);
		}
	}

	public function down(Schema $schema): void
	{
		foreach (
			// We only need the key part of the assoc array here
			array_keys(array: $this->schemas)
			as $tourneySchema
		) {
			$this->statement =
				<<<"SQL"
				DROP SCHEMA IF EXISTS {$tourneySchema} CASCADE
				SQL;

			$this->addSql(sql: $this->statement);
		}
	}
}
