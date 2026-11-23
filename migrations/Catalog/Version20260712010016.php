<?php

declare(strict_types=1);

namespace DoctrineMigrations\Catalog;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


/// --- Type hint namespaces --- ///
use function getenv;
use function sprintf;


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
final class Version20260712010016 extends AbstractMigration
{
	/** Db user. */
	private string $dbUser;
	/** Name. */
	private string $name = 'VOS';
	/** Schema. */
	private string $schema = '';
	/** Comment. */
	private string $comment = '';
	/** Statement. */
	private string $statement = '';

	/** Initialises the instance. */
	public function __construct()
	{
		$getEnvUser = getenv(name: 'DB_USER');

		$this->dbUser = $_ENV['DB_USER'] ?? ($getEnvUser !== false && $getEnvUser !== '' ? $getEnvUser : 'demo');
		$this->schema = sprintf(
			'%s_CATALOG',
			$this->name,
		);
		$this->comment = sprintf(
			'%s base domain schema.',
			$this->name,
		);
	}

	/** Reverts the migration. */
	public function down(Schema $schema): void
	{
		$this->statement = <<<SQL
			DROP SCHEMA IF EXISTS {$this->schema} CASCADE
			SQL;

		$this->addSql(sql: $this->statement);
	}

	/** Describes the migration. */
	public function getDescription(): string
	{
		return sprintf(
			'Create catalog schema to better categorise registered tournaments under %s org.',
			$this->name,
		);
	}

	/** Applies the migration. */
	public function up(Schema $schema): void
	{
		// This schema need to be created first
		$this->statement = <<<SQL
			CREATE SCHEMA IF NOT EXISTS
				{$this->schema}
			AUTHORIZATION
				"{$this->dbUser}"
			SQL;

		$this->addSql(sql: $this->statement);

		// Then we can add comment on the schema since it's existed now
		$this->statement = <<<SQL
			COMMENT ON SCHEMA
				{$this->schema}
			IS
				'{$this->comment}'
			SQL;

		$this->addSql(sql: $this->statement);
	}
}
