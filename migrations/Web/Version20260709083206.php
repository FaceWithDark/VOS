<?php

declare(strict_types=1);


namespace DoctrineMigrations\Web;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260709083206 extends AbstractMigration
{
	private string $name		= 'VOS';
	private string $schema		= '';
	private string $comment		= '';
	private string $statement	= '';

	public function __construct()
	{
		$this->schema	= sprintf(
			'%s_CATALOG',
			$this->name
		);
		$this->comment	= sprintf(
			'storing info about osu!taiko users that ARE NOT belong to any registered tournaments under %s org.',
			$this->name
		);
	}

	public function getDescription(): string
	{
		return sprintf(
			'Create `users` table for users that are not belong to any registered tournaments under %s org.',
			$this->name
		);
	}

	public function up(Schema $schema): void
	{
		// This table need to be created first
		$this->statement =
			// And UKs as well to make sure this's a 1:1 relationship:
			// https://www.beekeeperstudio.io/blog/one-to-one-database-relationships-complete-guide
			<<<"SQL"
			CREATE TABLE IF NOT EXISTS {$this->schema}.users (
				id INTEGER NOT NULL,
				role_id INTEGER NOT NULL,
				name TEXT NOT NULL,
				avatar TEXT NOT NULL,
				rank SMALLINT NOT NULL,
				country_flag VARCHAR(2) NOT NULL,
				create_on TIMESTAMP(0) WITH TIME ZONE NOT NULL,
				CONSTRAINT PK_USER_ID PRIMARY KEY (id),
				CONSTRAINT UK_ROLE_ID UNIQUE (role_id)
			)
			SQL;

		$this->addSql(sql: $this->statement);


		// Then we create the index on FKs for performance purposes:
		// https://www.beekeeperstudio.io/blog/one-to-one-database-relationships-complete-guide
		$this->statement =
			<<<"SQL"
			CREATE INDEX
				IDX_ROLE_ID
			ON
				{$this->schema}.users (role_id)
			SQL;

		$this->addSql(sql: $this->statement);

		// Finally we can add comment on the table since it's existed now
		$this->statement =
			<<<"SQL"
			COMMENT ON TABLE
				{$this->schema}.roles
			IS
				'{$this->comment}'
			SQL;

		$this->addSql(sql: $this->statement);


		// Also don't forget to create FKs to bond relationship between tables
		$this->statement =
			<<<"SQL"
			ALTER TABLE IF EXISTS
				{$this->schema}.users
			ADD
				CONSTRAINT
					FK_ROLE_ID FOREIGN KEY (role_id)
				REFERENCES
					{$this->schema}.roles (id)
				MATCH FULL
				ON UPDATE CASCADE
				ON DELETE NO ACTION
				NOT DEFERRABLE
			SQL;

		$this->addSql(sql: $this->statement);
	}

	public function down(Schema $schema): void
	{
		$this->statement =
			<<<"SQL"
			DROP TABLE IF EXISTS {$this->schema}.roles CASCADE;
			SQL;

		$this->addSql(sql: $this->statement);
	}
}
