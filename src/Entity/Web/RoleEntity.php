<?php

declare(strict_types=1);

namespace App\Entity\Web;


/// --- Main namespaces --- ///
use Doctrine\ORM\Mapping as ORM;


/// --- Type hint namespaces --- ///
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;


/// --- Internal namespaces --- ///
use App\Entity\Abstract\RoleAbstract;
use App\Repository\Web\RoleRepository;


#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(
	name: '`roles`',
	schema: 'vos_catalog',
	options: ['comment' => 'storing roles definition that ARE NOT belong to any registered tournaments under VOS org.']
)]
final class RoleEntity extends RoleAbstract
{
	#[ORM\Column(
		type: Types::STRING,
		length: 255,
		nullable: false
	)]
	private ?string $name = 'User';

	#[ORM\Column(
		type: Types::TEXT,
		nullable: true
	)]
	private ?string $description = 'can only interact with what exposed to the website.';

	/**
	 * @var Collection<int, UserEntity>
	 */
	#[ORM\OneToMany(
		targetEntity: UserEntity::class,
		mappedBy: 'roleId',
		cascade: ['persist', 'remove']
	)]
	private Collection $users;

	public function __construct()
	{
		parent::__construct();

		$this->users = new ArrayCollection();
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
	 * @return Collection<int, UserEntity>
	 */
	public function getUsers(): Collection
	{
		return $this->users;
	}

	public function addUser(UserEntity $user): static
	{
		if (!$this->users->contains($user)) {
			$this->users->add($user);
			$user->setRoleId($this);
		}

		return $this;
	}

	public function removeUser(UserEntity $user): static
	{
		// The owning side is NOT NULL, so a user cannot exist without a role:
		// detaching only drops the in-memory association.
		$this->users->removeElement($user);

		return $this;
	}
}
