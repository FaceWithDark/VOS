<?php

declare(strict_types=1);


namespace App\Entity\Abstract;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;


#[ORM\MappedSuperclass]
abstract class AbstractTournament {
	#[ORM\Id]
	#[ORM\Column(
		type: Types::INTEGER,
		nullable: false
	)]
	protected ?int $id = null;

	#[ORM\Column(
		type: Types::DATETIMETZ_MUTABLE,
		nullable: false
	)]
	protected ?\DateTime $createOn = null;

	protected function getId(): ?int
	{
		return $this->id;
	}

	protected function setId(int $id): static
	{
		$this->id = $id;

		return $this;
	}

	protected function getCreateOn(): ?\DateTime
	{
		return $this->createOn;
	}

	protected function setCreateOn(\DateTime $createOn): static
	{
		$this->createOn = $createOn;

		return $this;
	}
}
