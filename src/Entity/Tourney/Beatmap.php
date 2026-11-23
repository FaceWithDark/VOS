<?php

declare(strict_types=1);

namespace App\Entity\Tourney;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Abstract\AbstractBeatmap;
use App\Repository\Tourney\BeatmapRepository;


// @mago-format-ignore-end
#[ORM\Entity(repositoryClass: BeatmapRepository::class)]
#[ORM\Table(
	name: '`beatmaps`',
	options: [
		'comment' => 'storing beatmaps information used in a mappool within any registered tournaments under VOS org.',
	],
)]
final class Beatmap extends AbstractBeatmap
{
	/** Mod id. */
	#[ORM\ManyToOne(inversedBy: 'beatmaps')]
	#[ORM\JoinColumn(
		name: 'mod_id',
		referencedColumnName: 'id',
		nullable: false,
		onDelete: 'NO ACTION',
	)]
	private Mod $modId;

	/** Round id. */
	#[ORM\ManyToOne(inversedBy: 'beatmaps')]
	#[ORM\JoinColumn(
		name: 'round_id',
		referencedColumnName: 'id',
		nullable: false,
		onDelete: 'NO ACTION',
	)]
	private Round $roundId;

	/** The template beatmap data. */
	#[ORM\Column(
		type: Types::JSONB,
		nullable: false,
		options: [
			'comment' => 'Template beatmap data:
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
			```',
		],
	)]
	private mixed $details = null;

	/** Get details. */
	public function getDetails(): mixed
	{
		return $this->details;
	}

	/** Get mod id. */
	public function getModId(): Mod
	{
		return $this->modId;
	}

	/** Get round id. */
	public function getRoundId(): Round
	{
		return $this->roundId;
	}

	/** Set details. */
	public function setDetails(mixed $details): static
	{
		$this->details = $details;

		return $this;
	}

	/** Set mod id. */
	public function setModId(Mod $modId): static
	{
		$this->modId = $modId;

		return $this;
	}

	/** Set round id. */
	public function setRoundId(Round $roundId): static
	{
		$this->roundId = $roundId;

		return $this;
	}
}
