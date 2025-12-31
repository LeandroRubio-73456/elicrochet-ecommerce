<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'address_id',
        'total_amount',
        'status',
        'payphone_transaction_id',
        'payphone_status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'type', // standard (stock), catalog (no stock), custom
        'shipping_address', // If storing snapshot directly
        'shipping_city',
        'shipping_zip',
    ];

    // Types
    const TYPE_STOCK = 'standard';

    const TYPE_CATALOG = 'catalog';

    const TYPE_CUSTOM = 'custom';

    // Statuses
    const STATUS_QUOTATION = 'quotation'; // Cotización (Solo Custom)

    const STATUS_IN_CART = 'in_cart'; // Temporary status while in checkout

    const STATUS_PENDING_PAYMENT = 'pending_payment';

    const STATUS_PAID = 'paid';

    const STATUS_WORKING = 'working'; // En fabricación

    const STATUS_READY_TO_SHIP = 'ready_to_ship';

    const STATUS_SHIPPED = 'shipped';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    protected $casts = [
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get Display Order Number
     */
    public function getOrderNumberAttribute()
    {
        return 'PED-'.str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Recalculate total amount based on items
     */
    public function recalculateTotal()
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    // --- State Machine Logic ---

    /**
     * Check if order can transition to a target status
     */
    public function canTransitionTo($targetStatus)
    {
        // 1. Cancelled orders cannot change
        if ($this->status === self::STATUS_CANCELLED) {
            return false;
        }

        // 2. Cancellation rules
        if ($targetStatus === self::STATUS_CANCELLED) {
            return $this->canBeCancelled();
        }

        // 3. Status flow based on type
        return match ($this->type) {
            self::TYPE_STOCK => $this->checkStockFlow($targetStatus),
            self::TYPE_CATALOG => $this->checkCatalogFlow($targetStatus),
            self::TYPE_CUSTOM => $this->checkCustomFlow($targetStatus),
            default => false,
        };
    }

    private function canBeCancelled()
    {
        return in_array($this->status, [
            self::STATUS_QUOTATION,
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_PAID,
            self::STATUS_IN_CART,
        ]);
    }

    private function checkStockFlow($target)
    {
        $current = $this->status;

        // Pending -> Paid
        if ($current == self::STATUS_PENDING_PAYMENT && $target == self::STATUS_PAID) {
            return true;
        }
        // Paid -> Shipped
        if ($current == self::STATUS_PAID && $target == self::STATUS_SHIPPED) {
            return true;
        }
        // Legacy Ready -> Shipped
        if ($current == self::STATUS_READY_TO_SHIP && $target == self::STATUS_SHIPPED) {
            return true;
        }
        // Shipped -> Completed
        if ($current == self::STATUS_SHIPPED && $target == self::STATUS_COMPLETED) {
            return true;
        }

        return false;
    }

    private function checkCatalogFlow($target)
    {
        $current = $this->status;

        if ($current == self::STATUS_PENDING_PAYMENT && $target == self::STATUS_PAID) {
            return true;
        }
        if ($current == self::STATUS_PAID && $target == self::STATUS_WORKING) {
            return true;
        }
        if ($current == self::STATUS_WORKING && $target == self::STATUS_SHIPPED) {
            return true;
        }
        if ($current == self::STATUS_READY_TO_SHIP && $target == self::STATUS_SHIPPED) {
            return true;
        }
        if ($current == self::STATUS_SHIPPED && $target == self::STATUS_COMPLETED) {
            return true;
        }

        return false;
    }

    private function checkCustomFlow($target)
    {
        $current = $this->status;

        if ($current == self::STATUS_QUOTATION && $target == self::STATUS_PENDING_PAYMENT) {
            return true;
        }
        if ($current == self::STATUS_PENDING_PAYMENT && $target == self::STATUS_IN_CART) {
            return true;
        }
        if ($current == self::STATUS_IN_CART && $target == self::STATUS_PENDING_PAYMENT) {
            return true;
        }
        if ($current == self::STATUS_IN_CART && $target == self::STATUS_PAID) {
            return true;
        }
        if ($current == self::STATUS_PENDING_PAYMENT && $target == self::STATUS_PAID) {
            return true;
        }
        if ($current == self::STATUS_PAID && $target == self::STATUS_WORKING) {
            return true;
        }
        if ($current == self::STATUS_WORKING && $target == self::STATUS_SHIPPED) {
            return true;
        }
        if ($current == self::STATUS_READY_TO_SHIP && $target == self::STATUS_SHIPPED) {
            return true;
        }
        if ($current == self::STATUS_SHIPPED && $target == self::STATUS_COMPLETED) {
            return true;
        }

        return false;
    }
}
