<?php

declare(strict_types=1);


namespace DoctrineMigrations\Tourney;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260706092929 extends AbstractMigration
{
	private string	$name		= 'VOT';
	private array	$schemas	= [];
	private string	$statement	= '';

	public function __construct()
	{
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
			// Value is for table constraints name
			sprintf(
				'%s%s',
				$this->name,
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
				$this->name,
				$this->name,
				$iteration
			);

			$constraintName = sprintf(
				'%s%d',
				$this->name,
				$iteration
			);

			$this->schemas[$schemaName] = $constraintName;
		}

		/*
		 * Give the final array a sort so that the hard-coded value get placed in
		 * the right order
		 */
		ksort(
			$this->schemas,
			SORT_REGULAR
		);
	}

	public function getDescription(): string
	{
		return sprintf(
			'Create `users` tables across all iteration schemas for registered %s tournament.',
			$this->name
		);
	}

	public function up(Schema $schema): void
	{
		// This table need to be created in each iteration schema first
		foreach ($this->schemas as $tourneySchema => $tourneyConstraint) {
			$this->statement =
				<<<"SQL"
				CREATE TABLE IF NOT EXISTS {$tourneySchema}.users (
					id INTEGER NOT NULL,
					role_id INTEGER NOT NULL,
					name TEXT NOT NULL,
					avatar TEXT NOT NULL,
					rank SMALLINT NOT NULL,
					country_flag VARCHAR(2) NOT NULL,
					create_on TIMESTAMP(0) WITH TIME ZONE NOT NULL,
					CONSTRAINT PK_{$tourneyConstraint}_USER_ID PRIMARY KEY (id)
				)
				SQL;

			$this->addSql(sql: $this->statement);
		}


		// Then we create the index on FKs for performance purposes:
		// https://www.beekeeperstudio.io/blog/one-to-many-database-relationships-complete-guide-interactive/
		foreach ($this->schemas as $tourneySchema => $tourneyConstraint) {
			$this->statement =
				<<<"SQL"
				CREATE INDEX
					IDX_{$tourneyConstraint}_ROLE_ID
				ON
					{$tourneySchema}.users (role_id)
				SQL;

			$this->addSql(sql: $this->statement);
		}


		// Finally we can add comment on the table since they're existed now
		foreach (
			// We only need the key part of the assoc array here
			array_keys(array: $this->schemas)
			as $tourneySchema
		) {
			$this->statement =
				<<<"SQL"
				COMMENT ON TABLE
					{$tourneySchema}.users
				IS
					'storing info about osu!taiko users that ARE belong to one or more registered tournaments under VOS org.'
				SQL;

			$this->addSql(sql: $this->statement);
		}


		// Also don't forget to create FKs to bond relationship between tables
		foreach ($this->schemas as $tourneySchema => $tourneyConstraint) {
			$this->statement =
				<<<"SQL"
				ALTER TABLE IF EXISTS
					{$tourneySchema}.users
				ADD
					CONSTRAINT
						FK_{$tourneyConstraint}_ROLE_ID FOREIGN KEY (role_id)
					REFERENCES
						{$tourneySchema}.roles (id)
					MATCH FULL
					ON UPDATE CASCADE
					ON DELETE NO ACTION
					NOT DEFERRABLE
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
				DROP TABLE IF EXISTS {$tourneySchema}.users CASCADE;
				SQL;

			$this->addSql(sql: $this->statement);
		}
	}
}
