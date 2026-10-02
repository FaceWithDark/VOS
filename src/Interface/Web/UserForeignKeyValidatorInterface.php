<?php

declare(strict_types=1);

namespace App\Interface\Web;


/// --- Main namespaces --- ///


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Interface\Abstract\ForeignKeyValidatorInterface;


/**
 * NOTE:
 *
 * Its sole purpose is to give Symfony an unique type-hint that resolves
 * unambiguously to {@see UserForeignKeyValidator} (no YAML binding needed).
 * All behaviour is inherited from {@see ForeignKeyValidatorInterface}.
 */
interface UserForeignKeyValidatorInterface extends ForeignKeyValidatorInterface {}
