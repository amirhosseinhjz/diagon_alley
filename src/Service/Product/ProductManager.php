<?php

namespace App\Service\Product;

use App\Entity\Brand\Brand;
use App\Entity\Category\Category;
use App\Entity\ItemValue;
use App\Entity\Product\Product;
use App\Entity\Variant;
use App\Interface\Product\ProductManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class ProductManager implements ProductManagerInterface
{
    const validUpdates = ['name', 'category', 'description', 'brand'];

    private EntityManagerInterface $em;

    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }

    public function getRequestBody(Request $req)
    {
        return json_decode($req->getContent(), true);
    }

    public function serialize($data, array $groups): string
    {
        return $this->serializer->serialize($data, 'json', ['groups' => $groups]);
    }

    public function normalizeArray(array $array): array
    {
        if (array_key_exists("name", $array) == false) $array['name'] = null;
        if (array_key_exists("description", $array) == false) $array['description'] = null;
        if (array_key_exists("active", $array) == false) $array['active'] = true;
        if (array_key_exists("brand", $array) == false) $array['brand'] = null;
        if (array_key_exists("category", $array) == false) $array['category'] = null;
        return $array;
    }

    public function createEntityFromArray(array $validatedArray): array
    {
        try {
            $product = new Product();
            $product->setName($validatedArray['name']);
            $product->setDescription($validatedArray['description']);
            $product->setActive($validatedArray['active']);
            $brand = $validatedArray['brand'];
            if ($brand != null) $brand = $this->em->getRepository(Brand::class)->findOneById($brand);
            $product->setBrand($brand);
            $category = $validatedArray['category'];
            if ($category != null) $category = $this->em->getRepository(Category::class)->findOneById($validatedArray['category']);
            $product->setCategory($category);
            $this->em->getRepository(Product::class)->add($product, true);
            return ['entity' => $product];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function updateEntity(Product $product, array $updates): array
    {
        try {
            if (array_key_exists('category', $updates) == true) {
                $updates['category'] = $this->em->getRepository(Category::class)->findOneById($updates['category']);
            }
            if (array_key_exists('brand', $updates) == true) {
                $updates['brand'] = $this->em->getRepository(Category::class)->findOneById($updates['brand']);
            }
            foreach ($updates as $key => $value) {
                if (in_array($key, self::validUpdates) == false) throw new Exception('invalid operation');
                $product->setWithKeyValue($key, $value);
            }
            $this->em->persist($product);
            $this->em->flush();
            return ['entity' => $product];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function deleteById(int $id): array
    {
        try {
            $product = $this->em->getRepository(Product::class)->findOneById($id);
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

    public function addFeature(int $id, array $features): array
    {
        try {
            $product = $this->em->getRepository(Product::class)->findOneById($id);
            $validFeatures = self::getValidFeatureValuesByProduct($product);
            foreach ($features as $featureValue) {
                $featureKey = $featureValue->getItemFeature()->getId();
                if (array_key_exists($featureKey, $validFeatures) == false) throw new Exception('invalid feature found');
                if (in_array($featureValue, $validFeatures[$featureKey]) == false) throw new Exception('invalid feature value found');
                $itemValue = $this->em->getRepository(ItemValue::class)->findOneBy(['id' => $featureValue]);
                $product->addItemValue($itemValue);
            }
            $this->em->persist($product);
            $this->em->flush();
            return ['message' => 'features added'];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function getValidFeatureValuesByProduct(Product $product): array
    {
        $category = $product->getCategory();
        $validFeatures = [];
        foreach ($category->getFeatures() as $feature) {
            $featureId = $feature->getId();
            $featureValues = $feature->getItemValues();
            $featureValueIds = [];
            foreach ($featureValues as $featureValue) {
                $featureValueIds[] = $featureValue->getId();
            }
            $validFeatures[$featureId] = $featureValueIds;
        }
        return $validFeatures;
    }

    public function removeFeature(int $id, array $features): array
    {
        try {
            $product = $this->em->getRepository(Product::class)->findOneById($id);
            foreach ($features as $featureValue) {
                $itemValue = $this->em->getRepository(ItemValue::class)->findOneBy(['id' => $featureValue]);
                $product->removeItemValue($itemValue);
            }
            $this->em->persist($product);
            $this->em->flush();
            return ['message' => 'features removed'];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function toggleActivity(int $id, bool $active): array
    {
        try {
            $product = $this->em->getRepository(Product::class)->findOneById($id);
            foreach ($product->getVariants() as $variant) {
                $variant->setStatus($active);
                $this->em->persist($variant);
            }
            $product->setActive($active);
            $this->em->persist($product);
            $this->em->flush();
            return ['message' => 'product status changed'];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function findBrandProducts(array $options): array
    {
        return $this->em->getRepository(Product::class)->findProductByBrandId($options);
    }

    public function findCategoryProducts(array $options): array
    {
        return $this->em->getRepository(Product::class)->findProductsByCategoryId($options);
    }

    public function findById(int $id): ?Product
    {
        return $this->em->getRepository(Product::class)->findOneById($id);
    }
}
