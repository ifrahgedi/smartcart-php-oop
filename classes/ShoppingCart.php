<?php
require_once __DIR__ . '/CartItem.php';

class ShoppingCart
{
    private array $items = [];
    private float $discountPercent = 0;

    public function addProduct(Product $product, int $quantity = 1): void
    {
        $id = $product->getId();
        if (isset($this->items[$id])) {
            $current = $this->items[$id]->getQuantity();
            $this->items[$id]->setQuantity($current + $quantity);
            return;
        }
        $this->items[$id] = new CartItem($product, $quantity);
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        if (!isset($this->items[$productId])) return;
        if ($quantity <= 0) {
            $this->removeProduct($productId);
            return;
        }
        $this->items[$productId]->setQuantity($quantity);
    }

    public function removeProduct(int $productId): void
    {
        unset($this->items[$productId]);
    }

    public function getItems(): array { return $this->items; }

    public function getSubtotal(): float
    {
        return array_reduce($this->items, fn($sum, CartItem $item) => $sum + $item->getTotal(), 0.0);
    }

    public function setDiscount(float $percent): void
    {
        if ($percent < 0 || $percent > 50) {
            throw new InvalidArgumentException('Discount must be between 0% and 50%.');
        }
        $this->discountPercent = $percent;
    }

    public function getDiscountPercent(): float { return $this->discountPercent; }
    public function getDiscountAmount(): float { return $this->getSubtotal() * ($this->discountPercent / 100); }
    public function getTotal(): float { return $this->getSubtotal() - $this->getDiscountAmount(); }

    public function clear(): void
    {
        $this->items = [];
        $this->discountPercent = 0;
    }
}
