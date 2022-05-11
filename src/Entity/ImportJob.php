<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ImportJobRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImportJobRepository::class)]
class ImportJob
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $finishedAt;

    #[ORM\Column(type: 'string', length: 255)]
    private $filename;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'integer')]
    private $importedItemCount = 0;

    #[ORM\Column(type: 'json', nullable: true)]
    private $errors = [];

    #[ORM\Column(type: 'string')]
    private $status;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $duration;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $errorCount;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeImmutable $finishedAt): self
    {
        $this->finishedAt = $finishedAt;
        $this->duration = (int) $finishedAt->diff($this->createdAt, true)->format('%s');

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImportedItemCount(): ?int
    {
        return $this->importedItemCount;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function increment(): self
    {
        ++$this->importedItemCount;

        return $this;
    }

    public function addError(array $error): self
    {
        $this->errors[] = $error;
        ++$this->errorCount;

        return $this;
    }

    public function hasError(): bool
    {
        return count($this->errors) > 0;
    }

    public function getErrorCount(): ?int
    {
        return $this->errorCount;
    }
}
