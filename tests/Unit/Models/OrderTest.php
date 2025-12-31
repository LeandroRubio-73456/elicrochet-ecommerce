<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\OrderItem;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    /** @test */
    public function it_calculates_total_correctly()
    {
        // Mock items relationship
        $order = \Mockery::mock(Order::class)->makePartial();

        $item1 = new OrderItem(['price' => 10, 'quantity' => 2]);
        $item2 = new OrderItem(['price' => 20, 'quantity' => 1]);

        // We use setRelation to simulate the loaded relationship
        $order->setRelation('items', collect([$item1, $item2]));

        $this->assertEquals(40, $order->recalculateTotal());
    }

    /** @test */
    public function it_formats_order_number_attribute()
    {
        $order = new Order;
        $order->id = 123;

        $this->assertEquals('PED-00123', $order->order_number);
    }

    /** @test */
    public function it_validates_stock_order_transitions()
    {
        $order = new Order(['type' => Order::TYPE_STOCK]);

        // Pending -> Paid (Valid)
        $order->status = Order::STATUS_PENDING_PAYMENT;
        $this->assertTrue($order->canTransitionTo(Order::STATUS_PAID));

        // Pending -> Shipped (Invalid)
        $this->assertFalse($order->canTransitionTo(Order::STATUS_SHIPPED));

        // Paid -> Shipped (Valid)
        $order->status = Order::STATUS_PAID;
        $this->assertTrue($order->canTransitionTo(Order::STATUS_SHIPPED));

        // Shipped -> Completed (Valid)
        $order->status = Order::STATUS_SHIPPED;
        $this->assertTrue($order->canTransitionTo(Order::STATUS_COMPLETED));
    }

    /** @test */
    public function it_validates_catalog_order_transitions()
    {
        $order = new Order(['type' => Order::TYPE_CATALOG]);

        // Paid -> Working (Valid for Catalog/Pre-order)
        $order->status = Order::STATUS_PAID;
        $this->assertTrue($order->canTransitionTo(Order::STATUS_WORKING));

        // Working -> Shipped (Valid)
        $order->status = Order::STATUS_WORKING;
        $this->assertTrue($order->canTransitionTo(Order::STATUS_SHIPPED));
    }

    /** @test */
    public function it_validates_custom_order_transitions()
    {
        $order = new Order(['type' => Order::TYPE_CUSTOM]);

        // Quotation -> Pending Payment
        $order->status = Order::STATUS_QUOTATION;
        $this->assertTrue($order->canTransitionTo(Order::STATUS_PENDING_PAYMENT));

        // Pending Payment -> In Cart (Valid for custom merge)
        $order->status = Order::STATUS_PENDING_PAYMENT;
        $this->assertTrue($order->canTransitionTo(Order::STATUS_IN_CART));
    }

    /** @test */
    public function it_handles_cancellation_rules()
    {
        $order = new Order(['type' => Order::TYPE_STOCK]);

        // Can cancel if pending
        $order->status = Order::STATUS_PENDING_PAYMENT;
        $this->assertTrue($order->canTransitionTo(Order::STATUS_CANCELLED));

        // Cannot cancel if already cancelled
        $order->status = Order::STATUS_CANCELLED;
        $this->assertFalse($order->canTransitionTo(Order::STATUS_PAID));

        // Cannot change if cancelled
        $this->assertFalse($order->canTransitionTo(Order::STATUS_SHIPPED));
    }
}
