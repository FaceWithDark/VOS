<?php

declare(strict_types=1);


namespace App\Entity\Tourney;

use App\Entity\Abstract\AbstractRound;
use App\Repository\Tourney\RoundRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: RoundRepository::class)]
#[ORM\Table(
	name: '`rounds`',
	options: ['comment' => 'storing rounds definition for any registered tournaments under VOS org.']
)]
final class Round extends AbstractRound
{
	#[ORM\Column(
		type: Types::STRING,
		length: 255,
		nullable: false
	)]
	private ?string $name = null;

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
		mappedBy: 'roundId'
	)]
	private Collection $beatmaps;

	public function __construct()
	{
		$this->beatmaps = new ArrayCollection();
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
			$beatmap->setRoundId($this);
		}

		return $this;
	}

	public function removeBeatmap(Beatmap $beatmap): static
	{
		if ($this->beatmaps->removeElement($beatmap)) {
			// set the owning side to null (unless already changed)
			if ($beatmap->getRoundId() === $this) {
				$beatmap->setRoundId(null);
			}
		}

		return $this;
	}
}
