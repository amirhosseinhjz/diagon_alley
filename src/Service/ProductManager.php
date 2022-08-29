<?php

namespace App\Service;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\ItemValue;
use App\Entity\Variant;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Exception;
use App\Entity\Product;

class ProductManager
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

    public function addFeatureToProduct(string $productName, string $featureName, string $featureValueName)
    {
//        $product = $this->em->getRepository(Product::class)->findOneByName($productName);
//        $product = $this->em->getRepository(Product::class)->findOneByName($productName);
    }

    public function validateProductArray(array $unvalidatedArray): array
    {
        try {
            $name = trim($unvalidatedArray["name"]);
            if (!$name) throw new Exception("name cant be empty");
            if ($this->em->getRepository(Product::class)->findOneByName($name)) throw new Exception("name already exists");

            $category = trim($unvalidatedArray["category"]);
            if (!$category) throw new Exception('category cant be empty');
            $categoryEntity = $this->em->getRepository(Category::class)->findOneByName($category);
            if (!$categoryEntity) throw new Exception('invalid category');
            if ($categoryEntity->isLeaf() == false) throw new Exception('cant add products to non leaf categories');

            array_key_exists("type", $unvalidatedArray) ? $type = $unvalidatedArray["type"] : $type = "physical";
            $validTypes = ['physical', 'digital'];
            if (in_array($type, $validTypes) == false) throw new Exception('invalid type');

            array_key_exists("description", $unvalidatedArray) ? $description = $unvalidatedArray["description"] : $description = null;

            array_key_exists("brand", $unvalidatedArray) ? $brand = $unvalidatedArray["brand"] : $brand = null;
            if (!$this->em->getRepository(Brand::class)->findOneByName($brand)) throw new Exception('invalid brand name');

//            array_key_exists("itemValues", $unvalidatedArray) ? $itemValues = $unvalidatedArray["itemValues"] : $itemValues = [];
//            $validFeatures = [];
//            foreach ($categoryEntity->getFeatures() as $categoryFeature) {
//                $validFeatures[] = $categoryFeature->getName();
//            }
//            foreach ($itemValues as $key => $value) {
//                if (in_array($key, $validFeatures) == false) throw new Exception("invalid feature " . $key);
//            }

            array_key_exists("active", $unvalidatedArray) ? $active = $unvalidatedArray["active"] : $active = true;
            return [
                "name" => $name,
                "category" => $category,
                "type" => $type,
                "description" => $description,
                "brand" => $brand,
//                "itemValues" => $itemValues,
                "active" => $active
            ];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function update(string $name, array $updateInfo): array
    {
        try {
            $product = $this->em->getRepository(Product::class)->findOneByName($name);

            if (array_key_exists('name', $updateInfo)) {
                $newName = trim($updateInfo["name"]);
                if (!$newName) throw new Exception("name cant be empty");
                if ($this->em->getRepository(Product::class)->findOneByName($newName)) throw new Exception("name already exists");
                $product->setName($newName);
            }

            if (array_key_exists('category', $updateInfo)) {
                $category = trim($updateInfo["category"]);
                if (!$category) throw new Exception('category cant be empty');
                $categoryEntity = $this->em->getRepository(Category::class)->findOneByName($category);
                if (!$categoryEntity) throw new Exception('invalid category');
                if ($categoryEntity->isLeaf() == false) throw new Exception('cant add products to non leaf categories');
                $product->setCategory($categoryEntity);
            }

            if (array_key_exists("type", $updateInfo)) {
                $type = $updateInfo["type"];
                $validTypes = ['physical', 'digital'];
                if (in_array($type, $validTypes) == false) throw new Exception('invalid type');
                $product->setType($type);
            }

            if (array_key_exists("description", $updateInfo)) {
                $description = $updateInfo["description"];
                $product->setDescription($description);
            }

            if (array_key_exists("brand", $updateInfo)) {
                $brand = $updateInfo["brand"];
                if (!$this->em->getRepository(Brand::class)->findOneByName($brand)) throw new Exception('invalid brand name');
                $product->setBrand($brand);
            }
            $this->em->getRepository(Product::class)->add($product, true);

            return ['product' => $product];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function createEntityFromArray(array $validatedArray): Product
    {
        $product = new Product();
        $product->setName($validatedArray['name']);
        $product->setType($validatedArray['type']);
        $product->setDescription($validatedArray['description']);
        $product->setActive($validatedArray['active']);
        $category = $this->em->getRepository(Category::class)->findOneByName($validatedArray['category']);
        $product->setCategory($category);
        $brand = $validatedArray['brand'];
        if ($brand != null) {
            $brand = $this->em->getRepository(Brand::class)->findOneByName($brand);
        }
        $product->setBrand($brand);
        foreach ($validatedArray['itemValues'] as $key => $value) {
            $this->addFeatureToProduct($product->getName(), $key, $value);
        }
        return $product;
    }

    public function deleteByName(string $name): array
    {
        try {
            $product = $this->em->getRepository(Product::class)->findOneByName($name);
            $variants = $product->getVariants();
            $variantRepo = $this->em->getRepository(Variant::class);
            foreach ($variants as $variant) {
                $variantRepo->remove($variant, false);
            }
            if (count($product->getVariants()) != 0) throw new Exception('operation failed');
            $this->em->getRepository(Product::class)->remove($product, true);
            return ['message' => 'product deleted'];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function addFeature(string $name, array $features): array
    {
//        try {
//
//        } catch (Exception $exception) {
//            return ['error' => $exception->getMessage()];
//        }
    }

    public function removeFeature(string $name, array $features): array
    {
//        try {
//
//        } catch (Exception $exception) {
//            return ['error' => $exception->getMessage()];
//        }
    }

    public function toggleActivity(string $name, bool $active): array
    {
        try {

        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }
}
