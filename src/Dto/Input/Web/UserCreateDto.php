<?php

declare(strict_types=1);

namespace App\Dto\Input\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Symfony\Component\Validator\Constraints as Assert;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
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

	/** Name. */
	#[Assert\NotBlank]
	#[Assert\Type(['string'])]
	public string $name;

	/** Avatar. */
	#[Assert\NotBlank]
	#[Assert\Type(['string'])]
	public string $avatar;

	/** Rank. */
	#[Assert\NotNull]
	#[Assert\Type(['integer'])]
	#[Assert\Range(min: 0)]
	public int $rank;

	/** Country flag. */
	#[Assert\NotBlank]
	#[Assert\Type(['string'])]
	#[Assert\Length(exactly: 2)]
	public string $countryFlag;
}
