<?php

declare(strict_types=1);


namespace DoctrineMigrations\Web;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260712010019 extends AbstractMigration
{
	private string $name		= 'VOS';
	private string $schema		= '';
	private string $statement	= '';

	public function __construct()
	{
		$this->schema	= sprintf(
			'%s_CATALOG',
			$this->name
		);
	}

	public function getDescription(): string
	{
		return sprintf(
			'Turn the `users`.`role_id` relation into 1:N (many users per role) under %s org.',
			$this->name
		);
	}

	public function up(Schema $schema): void
	{
		// Drop the 1:1 uniqueness so several users can share one role. The
		// IDX_ROLE_ID index stays for FK lookup performance.
		$this->statement =
			<<<"SQL"
			ALTER TABLE IF EXISTS
				{$this->schema}.users
			DROP CONSTRAINT IF EXISTS UK_ROLE_ID
			SQL;

		$this->addSql(sql: $this->statement);
	}

	public function down(Schema $schema): void
	{
		$this->statement =
			<<<"SQL"
			ALTER TABLE IF EXISTS
				{$this->schema}.users
			ADD CONSTRAINT UK_ROLE_ID UNIQUE (role_id)
			SQL;

		$this->addSql(sql: $this->statement);
	}
}
