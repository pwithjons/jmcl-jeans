<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\DB;

/**
 * Centralizes order status transition rules so "cancelling an order
 * restocks its items" happens exactly once, regardless of which admin
 * screen (or, later, a customer-initiated cancellation) triggers it.
 */
class OrderService
{
    private const VALID_STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    public function updateStatus(Order $order, string $newStatus): Order
    {
        if (! in_array($newStatus, self::VALID_STATUSES, true)) {
            throw new \InvalidArgumentException("Invalid order status: {$newStatus}");
        }

        $wasCancelled = $order->order_status === 'cancelled';
        $isBeingCancelled = $newStatus === 'cancelled';

        return DB::transaction(function () use ($order, $newStatus, $wasCancelled, $isBeingCancelled) {
            // Cancelling a live order releases its reserved stock back to
            // inventory. Re-cancelling an already-cancelled order (or any
            // other transition) never touches stock again, so this can't
            // double-restock if an admin clicks the button twice.
            if ($isBeingCancelled && ! $wasCancelled) {
                $this->restockItems($order);
            }

            $order->update(['order_status' => $newStatus]);

            return $order;
        });
    }

    private function restockItems(Order $order): void
    {
        foreach ($order->items as $item) {
            if ($item->product_variation_id) {
                $item->variation()->increment('stock_quantity', $item->quantity);
            }
        }
    }
}
