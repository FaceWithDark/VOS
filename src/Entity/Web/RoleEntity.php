<?php

declare(strict_types=1);

namespace App\Entity\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Entity\Abstract\RoleAbstract;
use App\Repository\Web\RoleRepository;


// @mago-format-ignore-end
#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(
	name: '`roles`',
	schema: 'vos_catalog',
	options: ['comment' => 'storing roles definition that ARE NOT belong to any registered tournaments under VOS org.'],
)]
final class RoleEntity extends RoleAbstract
{
	/** Name. */
	#[ORM\Column(
		type: Types::STRING,
		length: 255,
		nullable: false,
	)]
	private string $name = 'User';

	/** Description. */
	#[ORM\Column(
		type: Types::TEXT,
		nullable: true,
	)]
	private ?string $description = 'can only interact with what exposed to the website.';

	/**
	 * @var Collection<int, UserEntity>
	 */
	#[ORM\OneToMany(
		targetEntity: UserEntity::class,
		mappedBy: 'roleId',
		cascade: ['persist', 'remove'],
	)]
	private Collection $users;

	/** Initialises the instance. */
	public function __construct()
	{
		parent::__construct();

		$this->users = new ArrayCollection();
	}

	/** Add user. */
	public function addUser(UserEntity $user): static
	{
		if (!$this->users->contains($user)) {
			$this->users->add($user);
			$user->setRoleId($this);
		}

		return $this;
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

	/**
	 * @return Collection<int, UserEntity>
	 */
	public function getUsers(): Collection
	{
		return $this->users;
	}

	/** Remove user. */
	public function removeUser(UserEntity $user): static
	{
		// The owning side is NOT NULL, so a user cannot exist without a role:
		// detaching only drops the in-memory association.
		$this->users->removeElement($user);

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
