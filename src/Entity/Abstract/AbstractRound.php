<?php

declare(strict_types=1);

namespace App\Entity\Abstract;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;
use DateTimeZone;


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
#[ORM\MappedSuperclass]
abstract class AbstractRound
{
	/** Id. */
	/** Id. */
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'IDENTITY')]
	#[ORM\Column(
		type: Types::INTEGER,
		nullable: false,
	)]
	protected ?int $id = null;

	/** Create on. */
	#[ORM\Column(
		type: Types::DATETIMETZ_IMMUTABLE,
		nullable: false,
	)]
	protected ?DateTimeImmutable $createOn = null;

	/** Initialises the instance. */
	public function __construct()
	{
		$this->createOn = new DateTimeImmutable(
			datetime: 'now',
			timezone: new DateTimeZone(timezone: 'UTC'),
		);
	}

	/** Get create on. */
	protected function getCreateOn(): ?DateTimeImmutable
	{
		return $this->createOn;
	}

	/** Get id. */
	protected function getId(): ?int
	{
		return $this->id;
	}

	/** Set create on. */
	protected function setCreateOn(DateTimeImmutable $createOn): static
	{
		$this->createOn = $createOn;

		return $this;
	}

	/** Set id. */
	protected function setId(int $id): static
	{
		$this->id = $id;

		return $this;
	}
}
