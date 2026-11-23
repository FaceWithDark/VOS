<?php

declare(strict_types=1);

namespace DoctrineMigrations\Tourney\Vtc;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


/// --- Type hint namespaces --- ///
use const SORT_REGULAR;
use function array_keys;
use function getenv;
use function ksort;
use function sprintf;


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
final class Version20260712010007 extends AbstractMigration
{
	/** Db user. */
	private string $dbUser;
	/** Name. */
	private string $name = 'VTC';
	/** Schemas. */
	private array $schemas = [];
	/** Statement. */
	private string $statement = '';

	/** Initialises the instance. */
	public function __construct()
	{
		$getenvUser = getenv(name: 'DB_USER');

		$this->dbUser = $_ENV['DB_USER'] ?? ($getenvUser !== false && $getenvUser !== '' ? $getenvUser : 'demo');

		for ($iteration = 1; $iteration <= 3; ++$iteration) {
			$schemaName = sprintf(
				'%s_%s%d',
				$this->name,
				$this->name,
				$iteration,
			);

			$schemaComment = sprintf(
				'%s%d iteration schema for registered %s tourney.',
				$this->name,
				$iteration,
				$this->name,
			);

			$this->schemas[$schemaName] = $schemaComment;
		}

		/*
		 * NOTE:
		 * keeping this since we might have to handle similar case later on like
		 * the migration script for registered VOT tourneys
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
				DROP SCHEMA IF EXISTS {$tourneySchema} CASCADE
				SQL;

			$this->addSql(sql: $this->statement);
		}
	}

	/** Describes the migration. */
	public function getDescription(): string
	{
		return sprintf(
			'Create iteration schemas for registered %s tournaments.',
			$this->name,
		);
	}

	/** Applies the migration. */
	public function up(Schema $schema): void
	{
		// This schema need to be created first
		foreach ($this->schemas as $name => $comment) {
			$this->statement = <<<SQL
				CREATE SCHEMA IF NOT EXISTS
					{$name}
				AUTHORIZATION
					"{$this->dbUser}"
				SQL;

			$this->addSql(sql: $this->statement);
		}

		// Then we can add comment on the schema since they're existed now
		foreach ($this->schemas as $name => $comment) {
			$this->statement = <<<SQL
				COMMENT ON SCHEMA
					{$name}
				IS
					'{$comment}'
				SQL;

			$this->addSql(sql: $this->statement);
		}
	}
}
