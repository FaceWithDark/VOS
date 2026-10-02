<?php

declare(strict_types=1);

namespace App\Dto\Input\Web;


/// --- Main namespaces --- ///
use Symfony\Component\Validator\Constraint as Assert;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


final class UserUpdateDto
{
	#[Assert\Type(['string', 'null'])]
	public ?string $name = null;

	/**
	 * Foreign key that points to an existing {@see \App\Entity\Web\RoleEntity} ID.
	 */
	#[Assert\Type(['integer', 'null'])]
	public ?int $roleId = null;

	#[Assert\Type(['string', 'null'])]
	public ?string $avatar = null;

	#[Assert\Type(['integer', 'null'])]
	#[Assert\Range(min: 0)]
	public ?int $rank = null;

	#[Assert\Type(['string', 'null'])]
	#[Assert\Length(exactly: 2)]
	public ?string $countryFlag = null;
}
