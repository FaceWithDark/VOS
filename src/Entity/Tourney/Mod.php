<?php

declare(strict_types=1);


namespace App\Entity\Tourney;

use App\Entity\Abstract\AbstractMod;
use App\Repository\Tourney\ModRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: ModRepository::class)]
#[ORM\Table(
	name: '`mods`',
	options: ['comment' => 'storing mods definition used in a mappool within any registered tournaments under VOS org.']
)]
final class Mod extends AbstractMod
{
	#[ORM\Column(
		type: Types::STRING,
		length: 5,
		nullable: false
	)]
	private string $name;

	#[ORM\Column(
		type: Types::TEXT,
		nullable: true
	)]
	private ?string $description = null;

	/**
	 * @var Collection<int, Beatmap>
	 */
	#[ORM\OneToMany(
		targetEntity: Beatmap::class,
		mappedBy: 'modId'
	)]
	private Collection $beatmaps;

	public function __construct()
	{
		parent::__construct();

		$this->beatmaps = new ArrayCollection();
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): static
	{
		$this->description = $description;

		return $this;
	}

	/**
	 * @return Collection<int, Beatmap>
	 */
	public function getBeatmaps(): Collection
	{
		return $this->beatmaps;
	}

	public function addBeatmap(Beatmap $beatmap): static
	{
		if (!$this->beatmaps->contains($beatmap)) {
			$this->beatmaps->add($beatmap);
			$beatmap->setModId($this);
		}

		return $this;
	}

	public function removeBeatmap(Beatmap $beatmap): static
	{
		// The owning side is NOT NULL, so a beatmap cannot exist without a mod:
		// detaching only drops the in-memory association.
		$this->beatmaps->removeElement($beatmap);

		return $this;
	}
}
