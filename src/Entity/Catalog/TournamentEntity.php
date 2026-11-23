<?php

declare(strict_types=1);

namespace App\Entity\Catalog;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Abstract\TournamentAbstract;
use App\Repository\Catalog\TournamentRepository;


// @mago-format-ignore-end
#[ORM\Entity(repositoryClass: TournamentRepository::class)]
#[ORM\Table(
	name: '`tournaments`',
	schema: 'vos_catalog',
	options: ['comment' => 'storing any registered tournaments under VOS org.'],
)]
final class TournamentEntity extends TournamentAbstract
{
	/** Name. */
	/** Name. */
	#[ORM\Column(
		type: Types::TEXT,
		nullable: false,
	)]
	private string $name;

	/** Description. */
	#[ORM\Column(
		type: Types::TEXT,
		nullable: true,
	)]
	private ?string $description = null;

	/** Describes the migration. */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/** Get name. */
	public function getName(): string
	{
		return $this->name;
	}

	/** Set description. */
	public function setDescription(?string $description): static
	{
		$this->description = $description;

		return $this;
	}

	/** Set name. */
	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}
}
