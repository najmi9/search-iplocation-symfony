<?php

declare(strict_types=1);

namespace App\Events;

use App\Entity\User;

class UserCreatedEvent
{
    private User $product;

    public function __construct(User $product)
    {
        $this->product = $product;
    }

    public function getProduct(): User
    {
        return $this->product;
    }
}