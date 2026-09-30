<?php
class Product
{
    private int $id;
    private string $name;
    private float $price;

    public function __construct(int $id, string $name, float $price)
    {
        $this->id = $id;
        $this->name = trim($name);
        $this->setPrice($price);
    }

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getPrice(): float { return $this->price; }

    public function setPrice(float $price): void
    {
        if ($price < 0) {
            throw new InvalidArgumentException('Price cannot be negative.');
        }
        $this->price = $price;
    }
}
