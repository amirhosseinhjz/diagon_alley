<?php

namespace App\Entity\Wallet;

use App\Entity\User\Customer;
use App\Entity\User\Seller;
use App\Repository\Wallet\WalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
class Wallet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['wallet_basic'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    #[Groups(['wallet_basic'])]
    private ?int $balance = 0;

    #[ORM\OneToOne(mappedBy: 'wallet', cascade: ['persist', 'remove'])]
    private ?Customer $customer = null;

    #[ORM\OneToOne(mappedBy: 'wallet', cascade: ['persist', 'remove'])]
    private ?Seller $seller = null;

//    #[ORM\OneToOne(inversedBy: 'wallet', cascade: ['persist', 'remove'])]
//    #[ORM\JoinColumn(nullable: false)]
//    #[Groups(['wallet_customer'])]
//    private ?Customer $customer = null;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBalance(): ?int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        // set the owning side of the relation if necessary
        if ($customer->getWallet() !== $this) {
            $customer->setWallet($this);
        }

        $this->customer = $customer;

        return $this;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(Seller $seller): self
    {
        // set the owning side of the relation if necessary
        if ($seller->getWallet() !== $this) {
            $seller->setWallet($this);
        }

        $this->seller = $seller;

        return $this;
    }
}