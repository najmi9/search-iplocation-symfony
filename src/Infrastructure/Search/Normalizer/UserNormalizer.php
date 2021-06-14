<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Normalizer;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class UserNormalizer implements ContextAwareNormalizerInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof User && 'search' === $format;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        if (!$object instanceof User) {
            throw new \InvalidArgumentException('Unexpected type for normalization, expected Product, got '.get_class($object));
        }

        return [
            'id' => (string) $object->getId(), // id required and should be string
            'url' => $this->urlGenerator->generate('user_show', ['id' => $object->getId()]),
        ];
    }
}
