<?php

declare(strict_types=1);

namespace App\Infrastructure\Search\Model;

class Product implements ModelInterface
{
    private $id;
    private string $name;
    private float $price;
    private string $description;
    private float $rating;
    private $createdAt;
    private string $image;
    private $store;

    /**
     * @param string|int $id
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @param Store|string[]
     */ 
    public function setStore($store): self
    {
        $this->store = $store;

        return $this;
    }

    /**
     * @param string|int $createdAt
     */ 
    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of id
     */ 
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the value of name
     */ 
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the value of price
     */ 
    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Get the value of rating
     */ 
    public function getRating(): float
    {
        return $this->rating;
    }

    /**
     * Get the value of createdAt
     * 
     * @return string|int
     */ 
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get the value of image
     */ 
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Get the value of store
     * 
     * @return Store|string[]
     */ 
    public function getStore()
    {
        return $this->store;
    }
}