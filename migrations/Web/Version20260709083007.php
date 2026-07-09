<?php

declare(strict_types=1);


namespace DoctrineMigrations\Web;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260709083007 extends AbstractMigration
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
			'Create `messenger_messages` table for catalog & relational schemas under %s org.',
			$this->name
		);
	}

	public function up(Schema $schema): void
	{
		// This table need to be created first
		$this->statement =
			<<<"SQL"
			CREATE TABLE IF NOT EXISTS {$this->schema}.messenger_messages (
				id BIGINT GENERATED ALWAYS AS IDENTITY NOT NULL,
				body TEXT NOT NULL,
				headers TEXT NOT NULL,
				queue_name VARCHAR(190) NOT NULL,
				created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
				available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
				delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
				CONSTRAINT PK_MESSENGER_MESSAGE_ID PRIMARY KEY (id)
			)
			SQL;

		$this->addSql(sql: $this->statement);


		// Not too sure why Doctrine Migration commands generate this, but
		// we'll just be safe and keep it as it is
		$this->statement =
			<<<"SQL"
			CREATE INDEX
				IDX_MESSENGER_MESSAGE_ID
			ON
				{$this->schema}.messenger_messages (
					queue_name,
					available_at,
					delivered_at,
					id
				)
			SQL;

		$this->addSql(sql: $this->statement);
	}

	public function down(Schema $schema): void
	{
		$this->statement =
			<<<"SQL"
			DROP TABLE IF EXISTS {$this->schema}.messenger_messages CASCADE;
			SQL;

		$this->addSql(sql: $this->statement);
	}
}
