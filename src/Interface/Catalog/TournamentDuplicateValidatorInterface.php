<?php

declare(strict_types=1);

namespace App\Interface\Catalog;

// @mago-format-ignore-start


/// --- Main namespaces --- ///


/// --- Type hint namespaces --- ///


/// --- Internal namespaces --- ///
use App\Interface\Abstract\DuplicateValidatorInterface;


// @mago-format-ignore-end
/**
 * NOTE:
 *
 * Its sole purpose is to give Symfony an unique type-hint that resolves
 * unambiguously to {@see TournamentDuplicatorValidator} (no YAML binding needed).
 * All behaviour is inherited from {@see DuplicateValidatorInterface}.
 */
interface TournamentDuplicateValidatorInterface extends DuplicateValidatorInterface {}
