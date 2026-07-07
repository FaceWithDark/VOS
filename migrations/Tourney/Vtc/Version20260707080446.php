<?php

declare(strict_types=1);


namespace DoctrineMigrations\Tourney\Vtc;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260707080446 extends AbstractMigration
{
	private string	$name		= 'VTC';
	private array	$schemas	= [];
	private string	$statement	= '';

	public function __construct()
	{
		for (
			$iteration = 1;
			$iteration <= 3;
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
			'Create `beatmaps` tables across all iteration schemas for registered %s tournament.',
			$this->name
		);
	}

	public function up(Schema $schema): void
	{
		// This table need to be created in each iteration schema first
		foreach ($this->schemas as $tourneySchema => $tourneyConstraint) {
			$this->statement =
				<<<"SQL"
				CREATE TABLE IF NOT EXISTS {$tourneySchema}.beatmaps (
					id INTEGER NOT NULL,
					mod_id INTEGER NOT NULL,
					round_id INTEGER NOT NULL,
					details JSONB NOT NULL,
					create_on TIMESTAMP(0) WITH TIME ZONE NOT NULL,
					CONSTRAINT PK_{$tourneyConstraint}_BEATMAP_ID PRIMARY KEY (id)
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
					IDX_{$tourneyConstraint}_MOD_ID
				ON
					{$tourneySchema}.beatmaps (mod_id)
				SQL;

			$this->addSql(sql: $this->statement);
		}


		foreach ($this->schemas as $tourneySchema => $tourneyConstraint) {
			$this->statement =
				<<<"SQL"
				CREATE INDEX
					IDX_{$tourneyConstraint}_ROUND_ID
				ON
					{$tourneySchema}.beatmaps (round_id)
				SQL;

			$this->addSql(sql: $this->statement);
		}


		// Finally we can add comment on the table/column since they are
		// existed now
		foreach (
			// We only need the key part of the assoc array here
			array_keys(array: $this->schemas)
			as $tourneySchema
		) {
			$this->statement =
				<<<"SQL"
				COMMENT ON TABLE
					{$tourneySchema}.beatmaps
				IS
					'storing beatmaps information used in a mappool within any registered tournaments under VOS org.'
				SQL;

			$this->addSql(sql: $this->statement);
		}



		foreach (
			// We only need the key part of the assoc array here
			array_keys(array: $this->schemas)
			as $tourneySchema
		) {
			$this->statement =
				<<<"SQL"
				COMMENT ON COLUMN
					{$tourneySchema}.beatmaps.details
				IS
					'Template beatmap data:
					```yaml
					name: string
					fa: string
					banner: string
					diff: string
					sr: float
					bpm: float
					length: string
					od: float
					hp: float
					mapper: string
					selector: string
					```'
				SQL;

			$this->addSql(sql: $this->statement);
		}


		// Also don't forget to create FKs to bond relationship between tables
		foreach ($this->schemas as $tourneySchema => $tourneyConstraint) {
			$this->statement =
				<<<"SQL"
				ALTER TABLE IF EXISTS
					{$tourneySchema}.beatmaps
				ADD
					CONSTRAINT
						FK_{$tourneyConstraint}_MOD_ID FOREIGN KEY (mod_id)
					REFERENCES
						{$tourneySchema}.mods (id)
					MATCH FULL
					ON UPDATE CASCADE
					ON DELETE NO ACTION
					NOT DEFERRABLE
				SQL;

			$this->addSql(sql: $this->statement);
		}


		foreach ($this->schemas as $tourneySchema => $tourneyConstraint) {
			$this->statement =
				<<<"SQL"
				ALTER TABLE IF EXISTS
					{$tourneySchema}.beatmaps
				ADD
					CONSTRAINT
						FK_{$tourneyConstraint}_ROUND_ID FOREIGN KEY (round_id)
					REFERENCES
						{$tourneySchema}.rounds (id)
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
				DROP TABLE IF EXISTS {$tourneySchema}.beatmaps CASCADE;
				SQL;

			$this->addSql(sql: $this->statement);
		}
	}
}
