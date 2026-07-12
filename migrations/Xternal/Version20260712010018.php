<?php

declare(strict_types=1);


namespace DoctrineMigrations\Xternal;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260712010018 extends AbstractMigration
{
	private array $prefixes = [
		'VOS',
		'VOT',
		'VTC'
	];
    private array $schemas = [];
    private string $statement = '';

    public function __construct()
    {
        foreach ($this->prefixes as $prefix) {
			if ($prefix === 'VOS') {
				// Catalog schemas setup (e.g., 'vos_catalog')
				$catalogSchema = sprintf(
					'%s_CATALOG',
					$prefix
				);
				$this->schemas[$prefix] = $catalogSchema;
			} elseif ($prefix == 'VOT') {
				// VOT schemas setup (e.g., 'vot_vot6')
				for (
					$iteration = 1;
					$iteration <= 6;
					$iteration++
				) {
					$constraintName = sprintf(
						'%s%d',
						$prefix,
						$iteration
					);
					$schemaName = sprintf(
						'%s_%s%d',
						$prefix,
						$prefix,
						$iteration
					);
					$this->schemas[$constraintName] = $schemaName;
				}

				// VOT schema have a special iteration with float-like number (e.g.,
				// 'vot_vot5_5')
				$specialConstraint = sprintf(
					'%s%s',
					$prefix,
					'5_5'
				);
				$specialSchema = sprintf(
					'%s_%s%s',
					$prefix,
					$prefix,
					'5_5'
				);
				$this->schemas[$specialConstraint] = $specialSchema;
			} elseif ($prefix == 'VTC') {
				// VTC schemas setup (e.g., 'vtc_vtc3')
				for (
					$iteration = 1;
					$iteration <= 3;
					$iteration++
				) {
					$constraintName = sprintf(
						'%s%d',
						$prefix,
						$iteration
					);
					$schemaName = sprintf(
						'%s_%s%d',
						$prefix,
						$prefix,
						$iteration
					);
					$this->schemas[$constraintName] = $schemaName;
				}
			} else {
				// Do nothing
				break;
			}
		}

        /*
         * Give the final assoc array a sort so that the hard-coded value gets
         * placed in the right order
         */
		ksort(
			array: $this->schemas,
			flags: SORT_REGULAR
		);
    }

    public function getDescription(): string
    {
		return sprintf(
			'Create `messenger_messages` tables across all catalog & relational schemas for registered tournament under %s org.',
			$this->prefixes[0]
		);
    }

    public function up(Schema $schema): void
    {
        foreach ($this->schemas as $tourneyConstraint => $tourneySchema) {
			// This table need to be created first
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


			// Not too sure why Doctrine Migration commands generate this, but
			// we'll just be safe and keep it as it is
            $this->statement =
                <<<"SQL"
                CREATE INDEX IF NOT EXISTS
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
