<?php

declare(strict_types=1);

namespace App\Events;

use App\Entity\Product;

class ProductUpdatedEvent
{
    private Product $product;
    private Product $previous;

    public function __construct(Product $product, Product $previous)
    {
        $this->product = $product;
        $this->previous = $previous;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getPrevious(): Product
    {
        return $this->previous;
    }
}