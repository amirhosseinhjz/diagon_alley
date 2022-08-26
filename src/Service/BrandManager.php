<?php

namespace App\Service;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Brand;

class BrandManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRequestBody(Request $req)
    {
        return json_decode($req->getContent(), true);
    }

    public function validateBrandArray(array $unvalidatedArray)
    {
        try {
            $name = trim($unvalidatedArray["name"]);
            array_key_exists("description", $unvalidatedArray)
                ? $description = $unvalidatedArray["description"]
                : $description = null;
            if (!$name) throw new Exception("name cant be empty");
            if ($this->em->getRepository(Brand::class)->findOneByName($name)) throw new Exception("name already exists");
            return [
                "name" => $name,
                "description" => $description
            ];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public
    function createEntityFromArray(array $validatedArray): Brand
    {
        $brand = new Brand();
        $brand->setName($validatedArray["name"]);
        $brand->setDescription($validatedArray["description"]);
        return $brand;
    }

    public
    function addRelationWithCategory(string $brandName, string $categoryName): Brand
    {
        $brand = new Brand();
        $brand->setName($brandName);
        $category = $this->em->getRepository(Category::class)->findOneByName($categoryName);
        $brand->addCategory($category);
        $this->em->persist($brand);
        $this->em->flush();
        return $brand;
    }

    public
    function removeRelationWithCategory(string $brandName, string $categoryName)
    {
        $brand = new Brand();
        $brand->setName($brandName);
        $category = $this->em->getRepository(Category::class)->findOneByName($categoryName);
        $brand->removeCategory($category);
        $this->em->persist($brand);
        $this->em->flush();
        return $brand;
    }

    public
    function update(string $name, array $updateInfo)
    {
        $brand = $this->em->getRepository(Brand::class)->findOneByName($name);
        if ($updateInfo['name'] != null) $brand->setName($updateInfo['name']);
        if ($updateInfo['description'] != null) $brand->setName($updateInfo['description']);
        $this->em->getRepository(Brand::class)->add($brand, true);
    }

    public
    function removeUnusedByName($name)
    {
        $brand = $this->em->getRepository(Brand::class)->findOneByName($name);
        if ($brand->getProducts()->isEmpty() == false) throw new Exception("brand has existing products");
        $this->em->getRepository(Brand::class)->remove($brand);
    }
}
