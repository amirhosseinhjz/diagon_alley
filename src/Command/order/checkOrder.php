<?php

namespace App\Command\order;

use App\Entity\Order\Purchase;
use App\Repository\OrderRepository\PurchaseRepository;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;


class checkOrder extends Command
{
    protected static $defaultName = 'app:order:check';

    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Checks that the order payment time has expired or not')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $orders = $this->em->getRepository(Purchase::class)->findBy(['status'=>Purchase::STATUS_PENDING]);
        $expiredTime = new DateTime(date("Y-m-d H:i:s", time()-3600));
        foreach($orders as $order)
        {
            if($order->getCreatedAt()<$expiredTime)
            {
                $order->setStatus(Purchase::STATUS_EXPIRED);
            }
        }
        $this->em->flush();

        return 0;
    }
}