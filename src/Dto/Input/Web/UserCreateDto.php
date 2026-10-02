<?php

declare(strict_types=1);

namespace App\Dto\Input\Web;


/// --- Main namespaces --- ///
use Symfony\Component\Validator\Constraint as Assert;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


final class UserCreateDto
{
	/**
	 * NOTE:
	 *
	 * Externally sourced osu! user identifier. It's NOT auto-incremented by the
	 * database (see `VOS_CATALOG.USERS` schema), hence why client MUST provide it.
	 */
	#[Assert\NotBlank]
	#[Assert\Type(['integer'])]
	public int $id;

	/**
	 * Foreign key that points to an existing {@see \App\Entity\Web\RoleEntity} ID.
	 */
	#[Assert\NotNull]
	#[Assert\Type(['integer'])]
	public int $roleId;

	#[Assert\NotBlank]
	#[Assert\Type(['string'])]
	public string $name;

	#[Assert\NotBlank]
	#[Assert\Type(['string'])]
	public string $avatar;

	#[Assert\NotNull]
	#[Assert\Type(['integer'])]
	#[Assert\Range(min: 0)]
	public int $rank;

	#[Assert\NotBlank]
	#[Assert\Type(['string'])]
	#[Assert\Length(exactly: 2)]
	public string $countryFlag;
}
