<?php

declare(strict_types=1);


namespace App\Entity\Web;

use App\Entity\Abstract\AbstractUser;
use App\Repository\Web\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(
	name: '`users`',
	schema: 'vos_catalog',
	options: ['comment' => 'storing info about osu!taiko users that ARE NOT belong to any registered tournaments under VOS org.']
)]
final class User extends AbstractUser
{
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
}
