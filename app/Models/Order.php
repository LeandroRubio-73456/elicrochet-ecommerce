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
        $current = $this->status;

        // Admin force override logic could be added, but stricter strict flow:

        // 1. Cancelled orders cannot change
        if ($current === self::STATUS_CANCELLED) {
            return false;
        }

        // 2. Cancellation rules
        if ($targetStatus === self::STATUS_CANCELLED) {
            // Can cancel if pending payment or paid (but not yet working/shipping)
            // Also allow cancelling if in_cart
            return in_array($current, [
                self::STATUS_QUOTATION,
                self::STATUS_PENDING_PAYMENT,
                self::STATUS_PAID,
                self::STATUS_IN_CART,
            ]);
        }

        switch ($this->type) {
            case self::TYPE_STOCK:
                // Scneario A: Pending -> Paid -> Shipped -> Completed
                // Skip ready_to_ship as per user request
                if ($current == self::STATUS_PENDING_PAYMENT && $targetStatus == self::STATUS_PAID) {
                    return true;
                }
                if ($current == self::STATUS_PAID && $targetStatus == self::STATUS_SHIPPED) {
                    return true;
                }
                // Allow escape from legacy ready_to_ship
                if ($current == self::STATUS_READY_TO_SHIP && $targetStatus == self::STATUS_SHIPPED) {
                    return true;
                }

                if ($current == self::STATUS_SHIPPED && $targetStatus == self::STATUS_COMPLETED) {
                    return true;
                }
                break;

            case self::TYPE_CATALOG:
                // Scenario B: Pending -> Paid -> Working -> Shipped -> Completed
                if ($current == self::STATUS_PENDING_PAYMENT && $targetStatus == self::STATUS_PAID) {
                    return true;
                }
                if ($current == self::STATUS_PAID && $targetStatus == self::STATUS_WORKING) {
                    return true;
                } // Start crafting
                if ($current == self::STATUS_WORKING && $targetStatus == self::STATUS_SHIPPED) {
                    return true;
                } // Finished & Shipped
                // Allow escape from legacy ready_to_ship
                if ($current == self::STATUS_READY_TO_SHIP && $targetStatus == self::STATUS_SHIPPED) {
                    return true;
                }

                if ($current == self::STATUS_SHIPPED && $targetStatus == self::STATUS_COMPLETED) {
                    return true;
                }
                break;

            case self::TYPE_CUSTOM:
                // Scenario C: Quotation -> Pending Payment -> Paid -> Working -> Shipped -> Completed
                if ($current == self::STATUS_QUOTATION && $targetStatus == self::STATUS_PENDING_PAYMENT) {
                    return true;
                } // Owner sets price

                // Allow moving to cart
                if ($current == self::STATUS_PENDING_PAYMENT && $targetStatus == self::STATUS_IN_CART) {
                    return true;
                }
                if ($current == self::STATUS_IN_CART && $targetStatus == self::STATUS_PENDING_PAYMENT) {
                    return true;
                } // Removed from cart
                if ($current == self::STATUS_IN_CART && $targetStatus == self::STATUS_PAID) {
                    return true;
                } // Paid from checkout

                if ($current == self::STATUS_PENDING_PAYMENT && $targetStatus == self::STATUS_PAID) {
                    return true;
                }
                if ($current == self::STATUS_PAID && $targetStatus == self::STATUS_WORKING) {
                    return true;
                }
                if ($current == self::STATUS_WORKING && $targetStatus == self::STATUS_SHIPPED) {
                    return true;
                }
                // Allow escape from legacy ready_to_ship
                if ($current == self::STATUS_READY_TO_SHIP && $targetStatus == self::STATUS_SHIPPED) {
                    return true;
                }

                if ($current == self::STATUS_SHIPPED && $targetStatus == self::STATUS_COMPLETED) {
                    return true;
                }
                break;

            default:
                break;
        }

        return false;
    }
}
