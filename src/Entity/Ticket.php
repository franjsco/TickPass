<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $serial;

    #[ORM\Column(type: 'datetime')]
    private $generation_date;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $deactivation_date;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $validation_date;

    #[ORM\ManyToOne(targetEntity: TicketTypology::class)]
    private $typology;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    #[ORM\Column(type: 'text', nullable: true)]
    private $note;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $pass_path;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    public function setSerial(string $serial): self
    {
        $this->serial = $serial;

        return $this;
    }

    public function getGenerationDate(): ?\DateTimeInterface
    {
        return $this->generation_date;
    }

    public function setGenerationDate(\DateTimeInterface $generation_date): self
    {
        $this->generation_date = $generation_date;

        return $this;
    }

    public function getDeactivationDate(): ?\DateTimeInterface
    {
        return $this->deactivation_date;
    }

    public function setDeactivationDate(?\DateTimeInterface $deactivation_date): self
    {
        $this->deactivation_date = $deactivation_date;

        return $this;
    }

    public function getValidationDate(): ?\DateTimeInterface
    {
        return $this->validation_date;
    }

    public function setValidationDate(?\DateTimeInterface $validation_date): self
    {
        $this->validation_date = $validation_date;

        return $this;
    }

    public function getTypology(): ?TicketTypology
    {
        return $this->typology;
    }

    public function setTypology(?TicketTypology $typology): self
    {
        $this->typology = $typology;

        return $this;
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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    #[ORM\PrePersist]
    public function setGeneratedSerial()
    {
        $this->serial = uniqid();
    }

    #[ORM\PrePersist]
    public function setGenerationDateAuto()
    {
        $this->generation_date = new \DateTime();
    }

    #[ORM\PrePersist]
    public function setInitalStatus()
    {
        $this->status = 'valid';
    }

    public function isValid() 
    {
        return $this->status === 'valid';
    }

    public function isValidated() 
    {
        return $this->status === 'validated';
    }

    public function isDeactivate() 
    {
        return $this->status === 'deactivate';
    }

    public function setAsDeactive()
    {
        $this->deactivation_date = new \DateTime();
        return $this->status = 'deactivate';
    }

    public function setAsValid()
    {
        return $this->status = 'valid';
    }

    public function setAsValidated()
    {
        if (!$this->validation_date) $this->validation_date = new \DateTime();
        return $this->status = 'validated';
    }

    public function getPassPath(): ?string
    {
        return $this->pass_path;
    }

    public function setPassPath(?string $pass_path): self
    {
        $this->pass_path = $pass_path;

        return $this;
    }
}
