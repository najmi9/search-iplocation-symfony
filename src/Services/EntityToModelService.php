<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Product;
use App\Infrastructure\Search\Model\Product as ProductModel;
use App\Infrastructure\Search\Model\Store as StoreModel;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EntityToModelService
{
    private NormalizerInterface $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @return array|ProductModel
     */
    public function product(Product $product, bool $isNormalized = true, bool $isForTypesense = false)
    {
        $model = new ProductModel();
        $model->setId((string) $product->getId())
            ->setImage($product->getImage())
            ->setPrice($product->getPrice())
            ->setName($product->getName())
            ->setRating($product->getRating())
            ->setCreatedAt($isForTypesense ? $product->getCreatedAt()->getTimestamp(): $product->getCreatedAt())
            ->setDescription($product->getDescription())
        ;

        if (!$isForTypesense) {
            $store = new StoreModel();
            $store->setName($product->getStore()->getName())
                ->setLocation($product->getStore()->getLocation())
                ->setId((string) $product->getStore()->getId())
            ;
        }

        if ($isForTypesense) {
            // typesense support only array of strings.
            $store = [
                (string) $product->getStore()->getId(),
                $product->getStore()->getName(),
                \implode(',', $product->getStore()->getLocation()),
            ];
        }

        $model->setStore($store);

        if ($isNormalized) {
            return $this->normalizer->normalize($model);
        }

        return $model;
    }

    public function user()
    {

    }
}