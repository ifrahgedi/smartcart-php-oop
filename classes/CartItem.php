<?php
require_once __DIR__ . '/Product.php';

class CartItem
{
    private Product $product;
    private int $quantity;

    public function __construct(Product $product, int $quantity = 1)
    {
        $this->product = $product;
        $this->setQuantity($quantity);
    }

    public function getProduct(): Product { return $this->product; }
    public function getQuantity(): int { return $this->quantity; }

    public function setQuantity(int $quantity): void
    {
        if ($quantity < 1) {
            throw new InvalidArgumentException('Quantity must be at least 1.');
        }
        $this->quantity = $quantity;
    }

    public function getTotal(): float
    {
        return $this->product->getPrice() * $this->quantity;
    }
}
