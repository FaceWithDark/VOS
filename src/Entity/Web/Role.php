<?php

declare(strict_types=1);


namespace App\Entity\Web;

use App\Repository\Web\RoleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(name: '`roles`')]
class Role
{
	#[ORM\Id]
	#[ORM\GeneratedValue(strategy: 'IDENTITY')]
	#[ORM\Column(
		type: Types::INTEGER,
		nullable: false
	)]
	private ?int $id = null;

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

	#[ORM\Column(
		type: Types::DATETIMETZ_MUTABLE,
		nullable: false
	)]
	private ?\DateTime $createOn = null;

	#[ORM\OneToOne(
		mappedBy: 'roleId',
		cascade: ['persist', 'remove']
	)]
	private ?User $users = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function setId(int $id): static
	{
		$this->id = $id;

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

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): static
	{
		$this->description = $description;

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
