<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Model;

interface ModelInterface
{
    public function getId(): string;

    public function setId($id);
}