<?php

declare(strict_types=1);

namespace App\Dto\Input\Web;

// @mago-format-ignore-start


/// --- Main namespaces --- ///
use Symfony\Component\Validator\Constraints as Assert;


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///


// @mago-format-ignore-end
final class UserUpdateDto
{
	/** Name. */
	#[Assert\Type(['string', 'null'])]
	public ?string $name = null;

	/**
	 * Foreign key that points to an existing {@see \App\Entity\Web\RoleEntity} ID.
	 */
	#[Assert\Type(['integer', 'null'])]
	public ?int $roleId = null;

	/** Avatar. */
	#[Assert\Type(['string', 'null'])]
	public ?string $avatar = null;

	/** Rank. */
	#[Assert\Type(['integer', 'null'])]
	#[Assert\Range(min: 0)]
	public ?int $rank = null;

	/** Country flag. */
	#[Assert\Type(['string', 'null'])]
	#[Assert\Length(exactly: 2)]
	public ?string $countryFlag = null;
}
