<?php

declare(strict_types=1);

namespace App\Entity\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Abstract\UserAbstract;
use App\Repository\Web\UserRepository;


// @mago-format-ignore-end
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(
	name: '`users`',
	schema: 'vos_catalog',
	options: [
		'comment' => 'storing info about osu!taiko users that ARE NOT belong to any registered tournaments under VOS org.',
	],
)]
final class UserEntity extends UserAbstract
{
	/** Role id. */
	#[ORM\ManyToOne(
		inversedBy: 'users',
		cascade: ['persist'],
	)]
	#[ORM\JoinColumn(
		name: 'role_id',
		referencedColumnName: 'id',
		nullable: false,
		onDelete: 'NO ACTION',
	)]
	private RoleEntity $roleId;

	/** Name. */
	#[ORM\Column(
		type: Types::TEXT,
		nullable: false,
	)]
	private string $name;

	/** Avatar. */
	#[ORM\Column(
		type: Types::TEXT,
		nullable: false,
	)]
	private string $avatar;

	/** Rank. */
	#[ORM\Column(
		type: Types::SMALLINT,
		nullable: false,
	)]
	private int $rank;

	/** Country flag. */
	#[ORM\Column(
		type: Types::STRING,
		length: 2,
		nullable: false,
	)]
	private string $countryFlag;

	/** Get avatar. */
	public function getAvatar(): string
	{
		return $this->avatar;
	}

	/** Get country flag. */
	public function getCountryFlag(): string
	{
		return $this->countryFlag;
	}

	/** Get name. */
	public function getName(): string
	{
		return $this->name;
	}

	/** Get rank. */
	public function getRank(): int
	{
		return $this->rank;
	}

	/** Get role id. */
	public function getRoleId(): RoleEntity
	{
		return $this->roleId;
	}

	/** Set avatar. */
	public function setAvatar(string $avatar): static
	{
		$this->avatar = $avatar;

		return $this;
	}

	/** Set country flag. */
	public function setCountryFlag(string $countryFlag): static
	{
		$this->countryFlag = $countryFlag;

		return $this;
	}

	/** Set name. */
	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	/** Set rank. */
	public function setRank(int $rank): static
	{
		$this->rank = $rank;

		return $this;
	}

	/** Set role id. */
	public function setRoleId(RoleEntity $roleId): static
	{
		$this->roleId = $roleId;

		return $this;
	}
}
