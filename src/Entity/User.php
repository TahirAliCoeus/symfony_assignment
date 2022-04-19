<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     * @Assert\Length(min=2,max=100, minMessage="Name should be at least {{ limit }} characters long",maxMessage="Name should not be longer than {{ limit }} characters")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="'{{ value }}' is not a valid email")
     */
    private $email;

    /**
     * @ORM\Column(type = "boolean", nullable=true, options={"default":0})
     */
    private $is_idle;

    /**
     * @ORM\Column(type ="datetime")
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity=Task::class,mappedBy="user",cascade={"remove"})
     */
    private $tasks;



    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIsIdle(): ?bool
    {
        return $this->is_idle;
    }

    public function setIsIdle(bool $is_idle): self
    {
        $this->is_idle = $is_idle;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getTasks()
    {
        return $this->tasks;
    }
}
