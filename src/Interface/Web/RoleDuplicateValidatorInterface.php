<?php

declare(strict_types=1);

namespace App\Interface\Web;


/// --- Main namespaces --- ///


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Interface\Abstract\DuplicateValidatorInterface;


/**
 * NOTE:
 *
 * Its sole purpose is to give Symfony an unique type-hint that resolves
 * unambiguously to {@see RoleDuplicatorValidator} (no YAML binding needed).
 * All behaviour is inherited from {@see DuplicateValidatorInterface}.
 */
interface RoleDuplicateValidatorInterface extends DuplicateValidatorInterface {}
