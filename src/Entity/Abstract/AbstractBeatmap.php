<?php

declare(strict_types=1);


namespace App\Entity\Abstract;

use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;


#[ORM\MappedSuperclass]
abstract class AbstractBeatmap {
	#[ORM\Id]
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

	protected function getId(): ?int
	{
		return $this->id;
	}

	protected function setId(int $id): static
	{
		$this->id = $id;

		return $this;
	}

	protected function getCreateOn(): ?\DateTimeImmutable
	{
		return $this->createOn;
	}

	protected function setCreateOn(\DateTimeImmutable $createOn): static
	{
		$this->createOn = $createOn;

		return $this;
	}
}
