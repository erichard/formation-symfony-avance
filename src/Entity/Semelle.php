<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Semelle
{
    use Nameable;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 3)]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }
}
