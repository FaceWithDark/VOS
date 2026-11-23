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
final class Version20260712010019 extends AbstractMigration
{
	/** Name. */
	private string $name = 'VOS';
	/** Schema. */
	private string $schema = '';
	/** Statement. */
	private string $statement = '';

	/** Initialises the instance. */
	public function __construct()
	{
		$this->schema = sprintf(
			'%s_CATALOG',
			$this->name,
		);
	}

	/** Reverts the migration. */
	public function down(Schema $schema): void
	{
		$this->statement = <<<SQL
			ALTER TABLE IF EXISTS
				{$this->schema}.users
			ADD CONSTRAINT UK_ROLE_ID UNIQUE (role_id)
			SQL;

		$this->addSql(sql: $this->statement);
	}

	/** Describes the migration. */
	public function getDescription(): string
	{
		return sprintf(
			'Turn the `users`.`role_id` relation into 1:N (many users per role) under %s org.',
			$this->name,
		);
	}

	/** Applies the migration. */
	public function up(Schema $schema): void
	{
		// Drop the 1:1 uniqueness so several users can share one role. The
		// IDX_ROLE_ID index stays for FK lookup performance.
		$this->statement = <<<SQL
			ALTER TABLE IF EXISTS
				{$this->schema}.users
			DROP CONSTRAINT IF EXISTS UK_ROLE_ID
			SQL;

		$this->addSql(sql: $this->statement);
	}
}
