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
use App\Entity\Abstract\RoleAbstract;
use App\Repository\Tourney\RoleRepository;


// @mago-format-ignore-end
#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(
	name: '`roles`',
	options: [
		'comment' => 'storing roles definition that ARE belong to one or more registered tournaments under VOS org.',
	],
)]
final class Role extends RoleAbstract
{
	/** Name. */
	/** Name. */
	#[ORM\Column(
		type: Types::STRING,
		length: 255,
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
	 * @var Collection<int, User>
	 */
	#[ORM\OneToMany(
		targetEntity: User::class,
		mappedBy: 'roleId',
	)]
	private Collection $users;

	/** Initialises the instance. */
	public function __construct()
	{
		parent::__construct();

		$this->users = new ArrayCollection();
	}

	/** Add user. */
	public function addUser(User $user): static
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
	 * @return Collection<int, User>
	 */
	public function getUsers(): Collection
	{
		return $this->users;
	}

	/** Remove user. */
	public function removeUser(User $user): static
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
