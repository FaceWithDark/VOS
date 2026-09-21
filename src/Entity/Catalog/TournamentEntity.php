<?php

declare(strict_types=1);

namespace App\Entity\Catalog;


/// --- Main namespaces --- ///
use Doctrine\ORM\Mapping as ORM;


/// --- Type hint namespaces --- ///
use Doctrine\DBAL\Types\Types;


/// --- Internal namespaces --- ///
use App\Entity\Abstract\TournamentAbstract;
use App\Repository\Catalog\TournamentRepository;


#[ORM\Entity(repositoryClass: TournamentRepository::class)]
#[ORM\Table(
	name: '`tournaments`',
	schema: 'vos_catalog',
	options: ['comment' => 'storing any registered tournaments under VOS org.']
)]
final class TournamentEntity extends TournamentAbstract
{
	#[ORM\Column(
		type: Types::TEXT,
		nullable: false
	)]
    private ?string $name = null;

	#[ORM\Column(
		type: Types::TEXT,
		nullable: true
	)]
    private ?string $description = null;

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
}
