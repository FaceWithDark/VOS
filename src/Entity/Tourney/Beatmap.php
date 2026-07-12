<?php

declare(strict_types=1);


namespace App\Entity\Tourney;

use App\Entity\Abstract\AbstractBeatmap;
use App\Repository\Tourney\BeatmapRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: BeatmapRepository::class)]
#[ORM\Table(
	name: '`beatmaps`',
	options: ['comment' => 'storing beatmaps information used in a mappool within any registered tournaments under VOS org.']
)]
final class Beatmap extends AbstractBeatmap
{
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
}
