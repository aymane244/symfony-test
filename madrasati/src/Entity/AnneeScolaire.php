<?php

namespace App\Entity;

use App\Repository\AnneeScolaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnneeScolaireRepository::class)]
class AnneeScolaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    public ?\DateTime $annee = null;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getAnnee(): ?\DateTime
    {
        return $this->annee;
    }

    public function setAnnee(\DateTime $annee): self
    {
        $this->annee = $annee;

        return $this;
    }
    public function __toString()
    {
        return  $this->annee->format('Y-m-d');
    }
}
