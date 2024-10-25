<?php

namespace App\Entity;

use App\Repository\ApplicationStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationStatusRepository::class)]
class ApplicationStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * @var Collection<int, Application>
     */
    #[ORM\OneToMany(targetEntity: Application::class, mappedBy: 'applicationStatus')]
    private Collection $application;

    public function __construct()
    {
        $this->application = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Application>
     */
    public function getApplication(): Collection
    {
        return $this->application;
    }

    public function addApplication(Application $application): static
    {
        if (!$this->application->contains($application)) {
            $this->application->add($application);
            $application->setApplicationStatus($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): static
    {
        if ($this->application->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getApplicationStatus() === $this) {
                $application->setApplicationStatus(null);
            }
        }

        return $this;
    }

    public function __toString(): string {
        return $this->status;
    }
}
