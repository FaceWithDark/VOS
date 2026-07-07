<?php

declare(strict_types=1);


namespace DoctrineMigrations\Tourney;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260706092901 extends AbstractMigration
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
			'Create `messenger_messages` tables across all iteration schemas for registered %s tournament.',
			$this->name
		);
	}

	public function up(Schema $schema): void
	{
		// This table need to be created in each iteration schema first
		foreach ($this->schemas as $tourneySchema => $tourneyConstraint) {
			$this->statement =
				<<<"SQL"
				CREATE TABLE IF NOT EXISTS {$tourneySchema}.messenger_messages (
					id BIGINT GENERATED ALWAYS AS IDENTITY NOT NULL,
					body TEXT NOT NULL,
					headers TEXT NOT NULL,
					queue_name VARCHAR(190) NOT NULL,
					created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
					available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
					delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
					CONSTRAINT PK_{$tourneyConstraint}_MESSENGER_MESSAGE_ID PRIMARY KEY (id)
				)
				SQL;

			$this->addSql(sql: $this->statement);
		}


		// Not too sure why Doctrine Migration commands generate this, but
		// we'll just be safe and keep it as it is
		foreach ($this->schemas as $tourneySchema => $tourneyConstraint) {
			$this->statement =
				<<<"SQL"
				CREATE INDEX
					IDX_{$tourneyConstraint}_MESSENGER_MESSAGE_ID
				ON
					{$tourneySchema}.messenger_messages (
						queue_name,
						available_at,
						delivered_at,
						id
					)
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
				DROP TABLE IF EXISTS {$tourneySchema}.messenger_messages CASCADE;
				SQL;

			$this->addSql(sql: $this->statement);
		}
	}
}
