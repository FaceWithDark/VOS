<?php

declare(strict_types=1);


namespace App\Entity\Tourney;

use App\Entity\Abstract\AbstractRole;
use App\Repository\Tourney\RoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(
	name: '`roles`',
	options: ['comment' => 'storing roles definition that ARE belong to one or more registered tournaments under VOS org.']
)]
final class Role extends AbstractRole
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
	 * @var Collection<int, User>
	 */
	#[ORM\OneToMany(
		targetEntity: User::class,
		mappedBy: 'roleId'
	)]
	private Collection $users;

	public function __construct()
	{
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
	 * @return Collection<int, User>
	 */
	public function getUsers(): Collection
	{
		return $this->users;
	}

	public function addUser(User $user): static
	{
		if (!$this->users->contains($user)) {
			$this->users->add($user);
			$user->setRoleId($this);
		}

		return $this;
	}

	public function removeUser(User $user): static
	{
		if ($this->users->removeElement($user)) {
			// set the owning side to null (unless already changed)
			if ($user->getRoleId() === $this) {
				$user->setRoleId(null);
			}
		}

		return $this;
	}
}
