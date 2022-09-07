<?php

namespace App\Service\Category;

use App\Entity\Category\Category;
use App\Entity\Product\Product;
use App\Entity\Feature\Feature;
use App\Interface\Category\CategoryManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;

class CategoryManager implements CategoryManagerInterface
{
    const validUpdates = ['name', 'type', 'parent', 'leaf'];

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
        if (array_key_exists("name", $array) == false) $array['name'] = null;
        if (array_key_exists("type", $array) == false) $array['type'] = null;
        if (array_key_exists("leaf", $array) == false) $array['leaf'] = false;
        if (array_key_exists("parent", $array) == false) $array['parent'] = null;
        return $array;
    }

    public function createEntityFromArray(array $validatedArray): array
    {
        try {
            $category = new Category();
            $category->setName($validatedArray['name']);
            $category->setType($validatedArray['type']);
            $parent = $validatedArray['parent'];
            if ($parent != null) {
                $parent = $this->em->getRepository(Category::class)->findOneById($parent);
            }
            $category->setParent($parent);
            $category->setLeaf($validatedArray['leaf']);
            $this->em->getRepository(Category::class)->add($category, true);
            return ['entity' => $category];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function findBrandsById(int $id): array
    {
        return $this->em->getRepository(Product::class)->findBrandsByCategoryId($id);
    }

    public function findParentsById(string $id): array
    {
        $repo = $this->em->getRepository(Category::class);
        $category = $repo->findOneById($id);
        $parents = [];
        while ($category->getParent() != null) {
            $category = $category->getParent();
            $parents[] = $category;
        }
        return $parents;
    }

    public function updateEntity(Category $category, array $updates): array
    {
        try {
            if (array_key_exists('parent', $updates) == true) {
                $updates['parent'] = $this->em->getRepository(Category::class)->findOneById($updates['parent']);
            }

            foreach ($updates as $key => $value) {
                if (in_array($key, self::validUpdates) == false) throw new Exception('invalid operation');
                $category->setWithKeyValue($key, $value);
            }
            $this->em->persist($category);
            $this->em->flush();
            return ['entity' => $category];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function removeUnused(Category $category): array
    {
        try {
            if (count($category->getChildren()) != 0) throw new Exception('category has existing children');
            if (count($category->getProducts()) != 0) throw new Exception('category has existing products');
            $this->em->getRepository(Category::class)->remove($category, true);
            return ['message' => 'category ' . $category->getName() . ' deleted'];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function toggleCategoryActivity(Category $category, bool $active)
    {
        $category->setActive($active);
        if ($category->isLeaf()) {
            $products = $category->getProducts();
            foreach ($products as $product) {
                $product->setActive($active);
                $variants = $product->getVariants();
                foreach ($variants as $variant) {
                    $variant->setStatus($active);
                    $this->em->persist($variant);
                }
                $this->em->persist($product);
            }
        } else {
            $children = $category->getChildren();
            foreach ($children as $child) {
                $this->toggleCategoryActivity($child, $active);
                $this->em->persist($child);
            }
        }
    }

    public function toggleActivity(int $id, bool $active)
    {
        $category = $this->em->getRepository(Category::class)->findOneById($id);
        $this->toggleCategoryActivity($category, $active);
        $this->em->persist($category);
        $this->em->flush();
    }

    public function getFeaturesById(int $id): array
    {
        //TODO add feature values
        $features = $this->em->getRepository(Category::class)->findOneById($id)->getFeatures();
        $featureIds = [];
        foreach ($features as $feature) {
            $featureIds[] = $feature->getId();
        }
        return $featureIds;
    }

    public function addFeatures(Category $category, array $features): array
    {
        try {
            $featureRepo = $this->em->getRepository(Feature::class);
            foreach ($features as $featureId) {
                $feature = $featureRepo->readFeatureById($featureId);
                if (!$feature) throw new Exception('invalid feature');
                $category->addFeature($feature);
            }
            $this->em->persist($category);
            $this->em->flush();
            return ['message' => 'features added'];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function removeFeatures(Category $category, array $features): array
    {
        try {
            $featureRepo = $this->em->getRepository(Feature::class);
            foreach ($features as $featureId) {
                $feature = $featureRepo->findOneBy(['id' => $featureId]);
                if (!$feature) throw new Exception('invalid feature');
                $category->removeFeature($feature);
            }
            $this->em->persist($category);
            $this->em->flush();
            return ['message' => 'features removed'];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function findById(int $id): ?Category
    {
        return $this->em->getRepository(Category::class)->findOneById($id);
    }

    public function findMainCategories(): array
    {
        return $this->em->getRepository(Category::class)->findMainCategories();
    }
}
