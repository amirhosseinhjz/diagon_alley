<?php

namespace App\Service\Brand;

use App\Entity\Brand\Brand;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use App\Interface\Brand\BrandManagerInterface;

class BrandManager implements BrandManagerInterface
{
    const validUpdates = ['name', 'description'];

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRequestBody(Request $req)
    {
        return json_decode($req->getContent(), true);
    }

    public function normalizeArray(array $array): array
    {
        if (array_key_exists("description", $array) == false) $array['description'] = null;
        if (array_key_exists("name", $array) == false) $array['name'] = null;
        return $array;
    }

    public function createEntityFromArray(array $validatedArray): array
    {
        try {
            $brand = new Brand();
            $brand->setName($validatedArray["name"]);
            $brand->setDescription($validatedArray["description"]);
            $this->em->getRepository(Brand::class)->add($brand, true);
            return ['entity' => $brand];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function updateEntity(Brand $brand, array $updates): array
    {
        try {
            foreach ($updates as $key => $value) {
                if (in_array($key, self::validUpdates) == false) throw new Exception('invalid operation');
                $brand->setWithKeyValue($key, $value);
            }
            $this->em->persist($brand);
            $this->em->flush();
            return ['entity' => $brand];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function removeUnused(Brand $brand): array
    {
        try {
            if ($brand->getProducts()->isEmpty() == false) throw new Exception("brand has existing products");
            $this->em->getRepository(Brand::class)->remove($brand, true);
            return ['message' => 'brand deleted'];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function findById(int $id): ?Brand
    {
        return $this->em->getRepository(Brand::class)->findOneById($id);
    }

    public function search(string $query): array
    {
        return $this->em->getRepository(Brand::class)->findManyByQuery($query);
    }
}
