<?php

declare(strict_types=1);

namespace DoctrineMigrations\Tourney\Vot;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


/// --- Type hint namespaces --- ///
use const SORT_REGULAR;
use function array_keys;
use function ksort;
use function sprintf;


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
final class Version20260712010006 extends AbstractMigration
{
	/** Name. */
	private string $name = 'VOT';
	/** Schemas. */
	private array $schemas = [];
	/** Statement. */
	private string $statement = '';

	/** Initialises the instance. */
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
				'5_5',
			) =>
				// Value is for table constraints name
				sprintf(
					'%s%s',
					$this->name,
					'5_5',
				),
		];

		for ($iteration = 1; $iteration <= 6; ++$iteration) {
			$schemaName = sprintf(
				'%s_%s%d',
				$this->name,
				$this->name,
				$iteration,
			);

			$constraintName = sprintf(
				'%s%d',
				$this->name,
				$iteration,
			);

			$this->schemas[$schemaName] = $constraintName;
		}

		/*
		 * Give the final array a sort so that the hard-coded value get placed in
		 * the right order
		 */
		ksort(
			$this->schemas,
			SORT_REGULAR,
		);
	}

	/** Reverts the migration. */
	public function down(Schema $schema): void
	{
		// We only need the key part of the assoc array here
		foreach (array_keys(array: $this->schemas) as $tourneySchema) {
			$this->statement = <<<SQL
				DROP TABLE IF EXISTS {$tourneySchema}.beatmaps CASCADE;
				SQL;

			$this->addSql(sql: $this->statement);
		}
	}

	/** Describes the migration. */
	public function getDescription(): string
	{
		return sprintf(
			'Create `beatmaps` tables across all iteration schemas for registered %s tournament.',
			$this->name,
		);
	}

	/** Applies the migration. */
	public function up(Schema $schema): void
	{
		// This table need to be created in each iteration schema first
		foreach ($this->schemas as $tourneySchema => $tourneyConstraint) {
			$this->statement = <<<SQL
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

		// Create both FK indexes in one pass over the schemas:
		// https://www.beekeeperstudio.io/blog/one-to-many-database-relationships-complete-guide-interactive/
		foreach ($this->schemas as $tourneySchema => $tourneyConstraint) {
			$this->statement = <<<SQL
				CREATE INDEX
					IDX_{$tourneyConstraint}_MOD_ID
				ON
					{$tourneySchema}.beatmaps (mod_id)
				SQL;

			$this->addSql(sql: $this->statement);

			$this->statement = <<<SQL
				CREATE INDEX
					IDX_{$tourneyConstraint}_ROUND_ID
				ON
					{$tourneySchema}.beatmaps (round_id)
				SQL;

			$this->addSql(sql: $this->statement);
		}

		// Then we can add comment on it since they're existed now

		// We only need the key part of the assoc array here
		foreach (array_keys(array: $this->schemas) as $tourneySchema) {
			$this->statement = <<<SQL
				COMMENT ON TABLE
					{$tourneySchema}.beatmaps
				IS
					'storing beatmaps information used in a mappool within any registered tournaments under VOS org.'
				SQL;

			$this->addSql(sql: $this->statement);

			$this->statement = <<<SQL
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

		// Create both foreign keys in one pass to close the relationship
		foreach ($this->schemas as $tourneySchema => $tourneyConstraint) {
			$this->statement = <<<SQL
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

			$this->statement = <<<SQL
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
}
