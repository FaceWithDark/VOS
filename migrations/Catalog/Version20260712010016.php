<?php

declare(strict_types=1);


namespace DoctrineMigrations\Catalog;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260712010016 extends AbstractMigration
{
	private string $dbUser;
	private string $name		= 'VOS';
	private string $schema		= '';
	private string $comment		= '';
	private string $statement	= '';

	public function __construct()
	{
		$this->dbUser
			=  $_ENV['DB_USER']
			?? getenv('DB_USER')
			?: 'demo';
		$this->schema	= sprintf(
			'%s_CATALOG',
			$this->name
		);
		$this->comment	= sprintf(
			'%s base domain schema.',
			$this->name
		);
	}

	public function getDescription(): string
	{
		return sprintf(
			'Create catalog schema to better categorise registered tournaments under %s org.',
			$this->name
		);
	}

	public function up(Schema $schema): void
	{
		// This schema need to be created first
		$this->statement =
			<<<"SQL"
			CREATE SCHEMA IF NOT EXISTS
				{$this->schema}
			AUTHORIZATION
				"{$this->dbUser}"
			SQL;

		$this->addSql(sql: $this->statement);


		// Then we can add comment on the schema since it's existed now
		$this->statement =
			<<<"SQL"
			COMMENT ON SCHEMA
				{$this->schema}
			IS
				'{$this->comment}'
			SQL;

		$this->addSql(sql: $this->statement);
	}

	public function down(Schema $schema): void
	{
		$this->statement =
			<<<"SQL"
			DROP SCHEMA IF EXISTS {$this->schema} CASCADE
			SQL;

		$this->addSql(sql: $this->statement);
	}
}
