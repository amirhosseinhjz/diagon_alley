<?php

namespace App\Entity\Wallet;

use App\Entity\User\Customer;
use App\Entity\User\Seller;
use App\Repository\Wallet\WalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
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
    #[Groups(['wallet_user'])]
    private ?Customer $customer = null;

    #[ORM\OneToOne(mappedBy: 'wallet', cascade: ['persist', 'remove'])]
    #[Groups(['wallet_user'])]
    private ?Seller $seller = null;

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
        if ($balance != 0) throw new Exception('invalid balance');
        $this->balance = $balance;

        return $this;
    }

    public function deposit(int $amount)
    {
        if ($amount < 0) throw new Exception('amount cant be negative');
        $prevBalance = $this->getBalance();
        $newBalance = $prevBalance + $amount;
        $this->setBalance($newBalance);
    }

    public function withdraw(int $amount)
    {
        if ($amount < 0) throw new Exception('amount cant be negative');
        $prevBalance = $this->getBalance();
        $newBalance = $prevBalance - $amount;
        if ($newBalance < 0) throw new Exception('not enough balance');
        $this->setBalance($newBalance);
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