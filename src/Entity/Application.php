<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $curriculum_vitae = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    private ?User $applicant = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    private ?Job $job = null;

    #[ORM\Column(length: 255)]
    private ?string $letter_of_motivation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurriculumVitae(): ?string
    {
        return $this->curriculum_vitae;
    }

    public function setCurriculumVitae(string $curriculum_vitae): static
    {
        $this->curriculum_vitae = $curriculum_vitae;

        return $this;
    }

    public function getApplicant(): ?User
    {
        return $this->applicant;
    }

    public function setApplicant(?User $applicant): static
    {
        $this->applicant = $applicant;

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getLetterOfMotivation(): ?string
    {
        return $this->letter_of_motivation;
    }

    public function setLetterOfMotivation(string $letter_of_motivation): static
    {
        $this->letter_of_motivation = $letter_of_motivation;

        return $this;
    }

    public function __toString(): string {
        return $this->getApplicant()->getFirstname() . " " . $this->getApplicant()->getLastname() . " applied for " . $this->getJob();
    }
}
