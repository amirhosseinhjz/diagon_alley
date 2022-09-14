<?php

namespace App\Entity\Order;

use App\Entity\Address\Address;
use App\Entity\Payment\Payment;
use App\Entity\User\Customer;
use App\Repository\OrderRepository\PurchaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_EXPIRED = 'expired';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Serializer\Groups(['Order.read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\Column]
    #[Serializer\Groups(['Order.read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 25)]
    private ?string $serialNumber = null;

    #[ORM\Column]
    #[Serializer\Groups(['Order.read'])]
    private ?int $totalPrice = null;

    #[ORM\ManyToOne]
    #[Serializer\Groups(['Order.read'])]
    private ?Address $address = null;

    #[ORM\Column(length: 10)]
    #[Serializer\Groups(['Order.read'])]
    private ?string $status = Purchase::STATUS_PENDING;

    #[ORM\OneToMany(mappedBy: 'purchase', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\OneToMany(mappedBy: 'purchase', targetEntity: PurchaseItem::class, orphanRemoval: true)]
    private Collection $purchaseItems;

    public function __construct()
    {
        $this->purchaseItems = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
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

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * @return Collection<int, PurchaseItem>
     */
    public function getPurchaseItems(): Collection
    {
        return $this->purchaseItems;
    }

    public function addPurchaseItem(PurchaseItem $purchaseItem): self
    {
        if (!$this->purchaseItems->contains($purchaseItem)) {
            $this->purchaseItems->add($purchaseItem);
            $purchaseItem->setPurchase($this);
        }

        return $this;
    }

    public function removePurchaseItem(PurchaseItem $purchaseItem): self
    {
        if ($this->purchaseItems->removeElement($purchaseItem)) {
            // set the owning side to null (unless already changed)
            if ($purchaseItem->getPurchase() === $this) {
                $purchaseItem->setPurchase(null);
            }
        }

        return $this;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue()
    {
        $this->setCreatedAt(new \DateTimeImmutable());
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

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

//    #[ORM\PrePersist]
//    public function setStatusOnCreate()
//    {
//        $this->setStatus(self::STATUS_PENDING);
//    }
    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setPurchase($this);
        }

        return $this;
    }

    public function isEditable()
    {
        if ($this->getStatus() != self::STATUS_PENDING) {
            throw new \Exception('Purchase is not editable.');
        }
    }

    public function isCancellable()
    {
        if (!$this->getStatus() == self::STATUS_PENDING &&
            !$this->getStatus() == self::STATUS_PAID) {
            throw new \Exception('Purchase is not cancellable.');
        }
    }


    public function setSerial()
    {
        $this->serialNumber = (string)$this->getId();
    }
}
