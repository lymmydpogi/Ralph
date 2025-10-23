<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Orders;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'This email is already registered.')]
#[ORM\Table(name: "users")]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(type: "string", length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: "string", length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: "customer", targetEntity: Orders::class, cascade: ["persist", "remove"])]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    // -------------------------
    // Getters and Setters
    // -------------------------

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user has at least ROLE_CLIENT
        if (empty($roles)) {
            $roles[] = 'ROLE_CLIENT';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Collection<int, Orders>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setCustomer($this);
        }
        return $this;
    }

    public function removeOrder(Orders $order): self
    {
        if ($this->orders->removeElement($order)) {
            if ($order->getCustomer() === $this) {
                $order->setCustomer(null);
            }
        }
        return $this;
    }
}
