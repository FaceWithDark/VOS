<?php

declare(strict_types=1);

namespace App\Entity\Abstract;


/// --- Main namespaces --- ///
use Doctrine\ORM\Mapping as ORM;


/// --- Type hint namespaces --- ///
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Types\Types;


/// --- Internal namespaces --- ///


#[ORM\MappedSuperclass]
abstract class TournamentAbstract {
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'IDENTITY')]
	#[ORM\Column(
		type: Types::INTEGER,
		nullable: false
	)]
	protected ?int $id = null;

	#[ORM\Column(
		type: Types::DATETIMETZ_IMMUTABLE,
		nullable: false
	)]
	protected ?\DateTimeImmutable $createOn = null;

	public function __construct()
	{
		$this->createOn = new DateTimeImmutable(
			datetime: 'now',
			timezone: new DateTimeZone(timezone: 'UTC')
		);
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function setId(int $id): static
	{
		$this->id = $id;

		return $this;
	}

	public function getCreateOn(): ?\DateTimeImmutable
	{
		return $this->createOn;
	}

	public function setCreateOn(\DateTimeImmutable $createOn): static
	{
		$this->createOn = $createOn;

		return $this;
	}
}
