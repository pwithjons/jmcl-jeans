<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\ProductVariation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Single source of truth for "whose cart is this" and "how many can they
 * add" so that logic isn't duplicated between the Cart page, Checkout,
 * and the login-merge step.
 */
class CartService
{
    private function ownerConstraint(Builder $query, ?string $sessionId = null): Builder
    {
        $userId = Auth::guard('web')->id();

        if ($userId) {
            return $query->where('user_id', $userId);
        }

        return $query->whereNull('user_id')->where('session_id', $sessionId ?? session()->getId());
    }

    public function items(): Collection
    {
        return $this->ownerConstraint(Cart::query())
            ->with(['product.images', 'variation'])
            ->get();
    }

    public function subtotal(): float
    {
        return $this->items()->sum(fn (Cart $item) => $item->line_total);
    }

    public function totalQuantity(): int
    {
        return (int) $this->ownerConstraint(Cart::query())->sum('quantity');
    }

    /**
     * Adds a line, or increases quantity if the same product+variation is
     * already in the cart. Clamps to available stock either way so the
     * cart can never hold more than what's actually purchasable.
     */
    public function add(int $productId, ?int $variationId, int $quantity): Cart
    {
        $stock = $this->stockFor($productId, $variationId);

        $existing = $this->ownerConstraint(Cart::query())
            ->where('product_id', $productId)
            ->where('product_variation_id', $variationId)
            ->first();

        if ($existing) {
            $existing->quantity = min($existing->quantity + $quantity, max($stock, 0));
            $existing->save();

            return $existing;
        }

        return Cart::create([
            'user_id' => Auth::guard('web')->id(),
            'session_id' => Auth::guard('web')->check() ? null : session()->getId(),
            'product_id' => $productId,
            'product_variation_id' => $variationId,
            'quantity' => min($quantity, max($stock, 1)),
        ]);
    }

    public function updateQuantity(int $cartId, int $quantity): void
    {
        $item = $this->ownerConstraint(Cart::query())->findOrFail($cartId);
        $stock = $this->stockFor($item->product_id, $item->product_variation_id);

        $item->update(['quantity' => max(1, min($quantity, max($stock, 1)))]);
    }

    public function remove(int $cartId): void
    {
        $this->ownerConstraint(Cart::query())->where('id', $cartId)->delete();
    }

    public function clear(): void
    {
        $this->ownerConstraint(Cart::query())->delete();
    }

    private function stockFor(int $productId, ?int $variationId): int
    {
        if ($variationId) {
            return (int) (ProductVariation::find($variationId)?->stock_quantity ?? 0);
        }

        return 0;
    }

    /**
     * Called right after a customer logs in or registers. Any cart rows
     * still tagged with their pre-login session_id are folded into their
     * account — matching product+variation lines have quantities summed
     * (clamped to stock), everything else is simply reassigned.
     */
    public function mergeGuestCartIntoUser(string $guestSessionId, int $userId): void
    {
        DB::transaction(function () use ($guestSessionId, $userId) {
            $guestItems = Cart::whereNull('user_id')->where('session_id', $guestSessionId)->get();

            foreach ($guestItems as $guestItem) {
                $existing = Cart::where('user_id', $userId)
                    ->where('product_id', $guestItem->product_id)
                    ->where('product_variation_id', $guestItem->product_variation_id)
                    ->first();

                if ($existing) {
                    $stock = $this->stockFor($guestItem->product_id, $guestItem->product_variation_id);
                    $existing->update(['quantity' => min($existing->quantity + $guestItem->quantity, max($stock, 1))]);
                    $guestItem->delete();
                } else {
                    $guestItem->update(['user_id' => $userId, 'session_id' => null]);
                }
            }
        });
    }
}
