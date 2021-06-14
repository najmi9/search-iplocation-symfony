<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Normalizer;

use App\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class ProductNormalizer implements ContextAwareNormalizerInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof Product && 'search' === $format;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Product) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Product, got '.get_class($object));
        }

        return [
            'id' => (string) $object->getId(), // id required and should be string
            'description' => $object->getShortDesciption(),
            'shortDescription' => $object->getShortDesciption(),
            'name' => $object->getName(),
            'category' => $object->getCategory() ? $object->getCategory()->getName() : '',
            'price' => $object->getPrice(),
            'created_at' => $object->getCreatedAt()->getTimestamp(),
            'url' => $this->urlGenerator->generate('product_show', ['id' => $object->getId()]),
        ];
    }
}
