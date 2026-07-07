<?php

declare(strict_types=1);


namespace App\Entity\Tourney;

use App\Repository\Tourney\ModRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: ModRepository::class)]
#[ORM\Table(
	name: '`mods`',
	options: ['comment' => 'storing mods definition used in a mappool within any registered tournaments under VOS org.']
)]
class Mod
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'IDENTITY')]
	#[ORM\Column(
		type: Types::INTEGER,
		nullable: false
	)]
	private ?int $id = null;

	#[ORM\Column(
		type: Types::STRING,
		length: 5,
		nullable: false
	)]
	private ?string $name = null;

	#[ORM\Column(
		type: Types::TEXT,
		nullable: true
	)]
	private ?string $description = null;

	#[ORM\Column(
		type: Types::DATETIMETZ_MUTABLE,
		nullable: false
	)]
	private ?\DateTime $createOn = null;

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
		$this->beatmaps = new ArrayCollection();
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

	public function getName(): ?string
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

	public function getCreateOn(): ?\DateTime
	{
		return $this->createOn;
	}

	public function setCreateOn(\DateTime $createOn): static
	{
		$this->createOn = $createOn;

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
		if ($this->beatmaps->removeElement($beatmap)) {
			// set the owning side to null (unless already changed)
			if ($beatmap->getModId() === $this) {
				$beatmap->setModId(null);
			}
		}

		return $this;
	}
}
