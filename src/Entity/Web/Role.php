<?php

declare(strict_types=1);


namespace App\Entity\Web;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Web\RoleRepository;
use App\Entity\Abstract\AbstractRole;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(
	name: '`roles`',
	schema: 'vos_catalog',
	options: ['comment' => 'storing roles definition that ARE NOT belong to any registered tournaments under VOS org.']
)]
final class Role extends AbstractRole
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

	#[ORM\OneToOne(
		mappedBy: 'roleId',
		cascade: ['persist', 'remove']
	)]
	private ?User $users = null;

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

	public function getUsers(): ?User
	{
		return $this->users;
	}

	public function setUsers(User $users): static
	{
		// set the owning side of the relation if necessary
		if ($users->getRoleId() !== $this) {
			$users->setRoleId($this);
		}

		$this->users = $users;

		return $this;
	}
}
