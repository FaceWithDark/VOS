<?php

declare(strict_types=1);


namespace App\Entity\Tourney;

use App\Repository\Tourney\BeatmapRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: BeatmapRepository::class)]
#[ORM\Table(
	name: '`beatmaps`',
	options: ['comment' => 'storing beatmaps information used in a mappool within any registered tournaments under VOS org.']
)]
class Beatmap
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(
		type: Types::INTEGER,
		nullable: false
	)]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: 'beatmaps')]
	#[ORM\JoinColumn(
		name: 'mod_id',
		referencedColumnName: 'id',
		nullable: false,
		onDelete: 'NO ACTION'
	)]
	private ?Mod $modId = null;

	#[ORM\ManyToOne(inversedBy: 'beatmaps')]
	#[ORM\JoinColumn(
		name: 'round_id',
		referencedColumnName: 'id',
		nullable: false,
		onDelete: 'NO ACTION'
	)]
	private ?Round $roundId = null;

	#[ORM\Column(
		type: Types::JSONB,
		nullable: false,
		options: [
			'comment' =>
			'Template beatmap data:
			```yaml
			name: "string"
			fa: "string"
			banner: "string"
			diff: "string"
			sr: float
			bpm: float
			length: "string"
			od: float
			hp: float
			mapper: "string"
			selector: "string"
			```'
		]
	)]
	private mixed $details = null;

	#[ORM\Column(
		type: Types::DATETIMETZ_MUTABLE,
		nullable: false
	)]
	private ?\DateTime $createOn = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function setId(int $id): static
	{
		$this->id = $id;

		return $this;
	}

	public function getModId(): ?Mod
	{
		return $this->modId;
	}

	public function setModId(?Mod $modId): static
	{
		$this->modId = $modId;

		return $this;
	}

	public function getRoundId(): ?Round
	{
		return $this->roundId;
	}

	public function setRoundId(?Round $roundId): static
	{
		$this->roundId = $roundId;

		return $this;
	}

	public function getDetails(): mixed
	{
		return $this->details;
	}

	public function setDetails(mixed $details): static
	{
		$this->details = $details;

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
}
