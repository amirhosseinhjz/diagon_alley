<?php

namespace App\Service;

use App\Entity\ItemFeature;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Exception;
use App\Entity\Category;

class CategoryManager
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

    public function validateCategoryArray(array $unvalidatedArray): array
    {
        try {
            $name = trim($unvalidatedArray["name"]);
            if (!$name) throw new Exception("name cant be empty");
            if ($this->em->getRepository(Category::class)->findOneByName($name)) throw new Exception("name already exists");
            array_key_exists("leaf", $unvalidatedArray) ? $leaf = $unvalidatedArray["leaf"] : $leaf = false;
            array_key_exists("parent", $unvalidatedArray) ? $parent = $unvalidatedArray["parent"] : $parent = null;
            if ($parent == null && $leaf == true) throw new Exception('main categories cant be leaf');
            if ($parent != null) {
                $parentEntity = $this->em->getRepository(Category::class)->findOneByName($parent);
                if ($parentEntity == null) throw new Exception("parent not found");
                if ($parentEntity->isLeaf()) throw new Exception("parent cant be leaf category");
            }
            return [
                "name" => $name,
                "leaf" => $leaf,
                "parent" => $parent
            ];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function createEntityFromArray(array $validatedArray): Category
    {
        $category = new Category();
        $category->setName($validatedArray['name']);
        $category->setLeaf($validatedArray['leaf']);
        $parent = $validatedArray['parent'];
        if ($parent != null) {
            $parent = $this->em->getRepository(Category::class)->findOneByName($parent);
        }
        $category->setParent($parent);
        return $category;
    }

    public function findBrandsByName(string $name): array
    {
        return $this->em->getRepository(Product::class)->findBrandsByCategoryName($name);
    }

    public function findParents(string $name): array
    {
        $repo = $this->em->getRepository(Category::class);
        $category = $repo->findOneByName($name);
        $parents = [];
        while ($category->getParent() != null) {
            $category = $category->getParent();
            $parents[] = $category->getName();
        }
        return $parents;
    }

    public function update(string $name, array $updateInfo)
    {
        try {
            $category = $this->em->getRepository(Category::class)->findOneByName($name);
            if (array_key_exists('name', $updateInfo)) {
                $newName = trim($updateInfo['name']);
                if (!$newName) throw new Exception('invalid name');
                if (!$this->em->getRepository(Category::class)->findOneByName($newName)) throw new Exception('new name already exists');
                $category->setName($newName);
            }

            if (array_key_exists('parent', $updateInfo)) {
                $parent = $this->em->getRepository(Category::class)->findOneByName($updateInfo['parent']);
                if (!$parent) throw new Exception('invalid parent');
                if ($parent->isLeaf()) throw new Exception('parent cant be leaf');
                $category->setParent($parent);
            }

            if (array_key_exists('leaf', $updateInfo)) {
                if ($updateInfo['leaf'] == true) {
                    if (count($category->getChildren()) != 0) throw new Exception('category has subcategories');
                    if ($category->getParent() == null) throw new Exception('parent cant be null');
                } else {
                    if (count($category->getProducts()) != 0) throw new Exception('category has existing products');
                }
                $category->setLeaf($updateInfo['leaf']);
            }

            $this->em->getRepository(Category::class)->add($category, true);
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function removeUnusedByName(string $name): array
    {
        try {
            $category = $this->em->getRepository(Category::class)->findOneByName($name);
            if (count($category->getChildren()) != 0) throw new Exception('category has existing children');
            if (count($category->getProducts()) != 0) throw new Exception('category has existing products');
            $this->em->getRepository(Category::class)->remove($category);
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
                $this->em->getRepository(Product::class)->add($product, false);
            }
        } else {
            $children = $category->getChildren();
            foreach ($children as $child) {
                $this->toggleCategoryActivity($child, $active);
                $this->em->getRepository(Category::class)->add($child);
            }
        }
    }

    public function toggleCategoryActivityByName(string $name, bool $active)
    {
        $category = $this->em->getRepository(Category::class)->findOneByName($name);
        $this->toggleCategoryActivity($category, $active);
        $this->em->getRepository(Category::class)->add($category, true);
    }

    public function getFeaturesByName(string $name): array
    {
        $features = $this->em->getRepository(Category::class)->findOneByName($name)->getFeatures();
        $featureNames = [];
        foreach ($features as $feature) {
            $featureNames[] = $feature->getName();
        }
        return $featureNames;
    }

    public function addFeatures(array $features): array
    {
        try {
            $category = $this->em->getRepository(Category::class)->findOneByName($features['name']);
            $featureRepo = $this->em->getRepository(ItemFeature::class);
            foreach ($features['features'] as $featureName) {
                $feature = $featureRepo->findOneBy(['name' => $featureName]);
                if (!$feature) throw new Exception('invalid feature');
                $category->addFeature($feature);
            }
            $this->em->getRepository(Category::class)->add($category, true);
            return ['message' => 'features added'];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }

    public function removeFeatures(array $features): array
    {
        try {
            $category = $this->em->getRepository(Category::class)->findOneByName($features['name']);
            $featureRepo = $this->em->getRepository(ItemFeature::class);
            foreach ($features['features'] as $featureName) {
                $feature = $featureRepo->findOneBy(['name' => $featureName]);
                if (!$feature) throw new Exception('invalid feature');
                $category->removeFeature($feature);
            }
            $this->em->getRepository(Category::class)->add($category, true);
            return ['message' => 'features removed'];
        } catch (Exception $exception) {
            return ['error' => $exception->getMessage()];
        }
    }
}
