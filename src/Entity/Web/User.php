<?php

declare(strict_types=1);


namespace App\Entity\Web;

use App\Repository\Web\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(
	name: '`users`',
	options: ['comment' => 'storing info about osu!taiko users that ARE NOT belong to any registered tournaments under VOS org.']
)]
class User
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'NONE')]
	#[ORM\Column(
		type: Types::INTEGER,
		nullable: false
	)]
	private ?int $id = null;

	#[ORM\OneToOne(
		inversedBy: 'users',
		cascade: ['persist', 'remove']
	)]
	#[ORM\JoinColumn(
		name: 'role_id',
		referencedColumnName: 'id',
		unique: true,
		nullable: false,
		onDelete: 'NO ACTION'
	)]
	private ?Role $roleId = null;

	#[ORM\Column(
		type: Types::TEXT,
		nullable: false
	)]
	private ?string $name = null;

	#[ORM\Column(
		type: Types::TEXT,
		nullable: false
	)]
	private ?string $avatar = null;

	#[ORM\Column(
		type: Types::SMALLINT,
		nullable: false
	)]
	private ?int $rank = null;

	#[ORM\Column(
		type: Types::STRING,
		length: 2,
		nullable: false
	)]
	private ?string $countryFlag = null;

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

	public function getRoleId(): ?Role
	{
		return $this->roleId;
	}

	public function setRoleId(Role $roleId): static
	{
		$this->roleId = $roleId;

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

	public function getAvatar(): ?string
	{
		return $this->avatar;
	}

	public function setAvatar(string $avatar): static
	{
		$this->avatar = $avatar;

		return $this;
	}

	public function getRank(): ?int
	{
		return $this->rank;
	}

	public function setRank(int $rank): static
	{
		$this->rank = $rank;

		return $this;
	}

	public function getCountryFlag(): ?string
	{
		return $this->countryFlag;
	}

	public function setCountryFlag(string $countryFlag): static
	{
		$this->countryFlag = $countryFlag;

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
