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

    public function validateBrandArray(array $unvalidatedArray): array
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

    public function createEntityFromArray(array $validatedArray): Brand
    {
        $brand = new Brand();
        $brand->setName($validatedArray["name"]);
        $brand->setDescription($validatedArray["description"]);
        return $brand;
    }

    public function update(string $name, array $updateInfo)
    {
        try {
            $brand = $this->em->getRepository(Brand::class)->findOneByName($name);
            if (!$brand) throw new Exception('brand not found');
            if (array_key_exists('name', $updateInfo)) {
                $newName = $updateInfo['name'];
                if (!$newName) throw new Exception('invalid name');
                if ($this->em->getRepository(Brand::class)->findOneByName($newName)) throw new Exception('name already exists');
                $brand->setName($newName);
            }
            if (array_key_exists('description', $updateInfo)) $brand->setName($updateInfo['description']);
            $this->em->getRepository(Brand::class)->add($brand, true);
            return ['message' => 'brand updated'];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function removeUnusedByName($name): array
    {
        try {
            $brand = $this->em->getRepository(Brand::class)->findOneByName($name);
            if (!$brand) throw new Exception('brand not found');
            if ($brand->getProducts()->isEmpty() == false) throw new Exception("brand has existing products");
            $this->em->getRepository(Brand::class)->remove($brand, true);
            return ['message' => 'brand deleted'];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }
}
