<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use App\Entity\Product;

class ProductManager
{
    private EntityManagerInterface $em;
    private Serializer $serializer;
}
