<?php

declare(strict_types=1);

namespace App\Entity\Tourney;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Abstract\AbstractMod;
use App\Repository\Tourney\ModRepository;


// @mago-format-ignore-end
#[ORM\Entity(repositoryClass: ModRepository::class)]
#[ORM\Table(
	name: '`mods`',
	options: [
		'comment' => 'storing mods definition used in a mappool within any registered tournaments under VOS org.',
	],
)]
final class Mod extends AbstractMod
{
	/** Name. */
	#[ORM\Column(
		type: Types::STRING,
		length: 5,
		nullable: false,
	)]
	private string $name;

	/** Description. */
	#[ORM\Column(
		type: Types::TEXT,
		nullable: true,
	)]
	private ?string $description = null;

	/**
	 * @var Collection<int, Beatmap>
	 */
	#[ORM\OneToMany(
		targetEntity: Beatmap::class,
		mappedBy: 'modId',
	)]
	private Collection $beatmaps;

	/** Initialises the instance. */
	public function __construct()
	{
		parent::__construct();

		$this->beatmaps = new ArrayCollection();
	}

	/** Add beatmap. */
	public function addBeatmap(Beatmap $beatmap): static
	{
		if (!$this->beatmaps->contains($beatmap)) {
			$this->beatmaps->add($beatmap);
			$beatmap->setModId($this);
		}

		return $this;
	}

	/**
	 * @return Collection<int, Beatmap>
	 */
	public function getBeatmaps(): Collection
	{
		return $this->beatmaps;
	}

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

	/** Remove beatmap. */
	public function removeBeatmap(Beatmap $beatmap): static
	{
		// The owning side is NOT NULL, so a beatmap cannot exist without a mod:
		// detaching only drops the in-memory association.
		$this->beatmaps->removeElement($beatmap);

		return $this;
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
