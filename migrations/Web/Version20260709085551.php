<?php

declare(strict_types=1);


namespace DoctrineMigrations\Web;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260709085551 extends AbstractMigration
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
			'storing any registered tournaments under %s org.',
			$this->name
		);
	}

	public function getDescription(): string
	{
		return sprintf(
			'Create `tournaments` table to record metadata about registered tournaments under %s org.',
			$this->name
		);
	}

	public function up(Schema $schema): void
	{
		// This table need to be created first
		$this->statement =
			<<<"SQL"
			CREATE TABLE IF NOT EXISTS {$this->schema}.tournaments (
				id INTEGER GENERATED ALWAYS AS IDENTITY NOT NULL,
				name TEXT NOT NULL,
				description TEXT DEFAULT NULL,
				create_on TIMESTAMP(0) WITH TIME ZONE NOT NULL,
				CONSTRAINT PK_TOURNAMENT_ID PRIMARY KEY (id)
			);
			SQL;

		$this->addSql(sql: $this->statement);


		// Then we can add comment on it since it's existed now
		$this->statement =
			<<<"SQL"
			COMMENT ON TABLE
				{$this->schema}.tournaments
			IS
				'{$this->comment}'
			SQL;

		$this->addSql(sql: $this->statement);
	}

	public function down(Schema $schema): void
	{
		$this->statement =
			<<<"SQL"
			DROP TABLE IF EXISTS {$this->schema}.tournaments CASCADE;
			SQL;

		$this->addSql(sql: $this->statement);
	}
}
