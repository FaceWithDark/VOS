<?php

declare(strict_types=1);

namespace DoctrineMigrations\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


/// --- Type hint namespaces --- ///
use function sprintf;


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
final class Version20260712010014 extends AbstractMigration
{
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
		$this->schema = sprintf(
			'%s_CATALOG',
			$this->name,
		);
		$this->comment = sprintf(
			'storing roles definition that ARE NOT belong to any registered tournaments under %s org.',
			$this->name,
		);
	}

	/** Reverts the migration. */
	public function down(Schema $schema): void
	{
		$this->statement = <<<SQL
			DROP TABLE IF EXISTS {$this->schema}.roles CASCADE;
			SQL;

		$this->addSql(sql: $this->statement);
	}

	/** Describes the migration. */
	public function getDescription(): string
	{
		return sprintf(
			'Create `roles` table for roles that are not tight to any registered tournaments under %s org.',
			$this->name,
		);
	}

	/** Applies the migration. */
	public function up(Schema $schema): void
	{
		// This table need to be created first
		$this->statement = <<<SQL
			CREATE TABLE IF NOT EXISTS {$this->schema}.roles (
				id INTEGER GENERATED ALWAYS AS IDENTITY NOT NULL,
				name VARCHAR(255) NOT NULL DEFAULT 'user',
				description TEXT DEFAULT NULL,
				create_on TIMESTAMP(0) WITH TIME ZONE NOT NULL,
				CONSTRAINT PK_ROLE_ID PRIMARY KEY (id)
			);
			SQL;

		$this->addSql(sql: $this->statement);

		// Then we can add comment on it since it's existed now
		$this->statement = <<<SQL
			COMMENT ON TABLE
				{$this->schema}.roles
			IS
				'{$this->comment}'
			SQL;

		$this->addSql(sql: $this->statement);
	}
}
