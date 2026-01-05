<?php

namespace Tests\Feature;

use App\Mail\CustomOrderReceived;
use App\Mail\NewOrderAdminNotification;
use App\Mail\OrderPaidNotification;
use App\Mail\OrderShippedNotification;
use App\Mail\PriceAssignedNotification;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MailableTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function custom_order_received_mailable_content()
    {
        $order = Order::factory()->create(['type' => Order::TYPE_CUSTOM]);
        $mailable = new CustomOrderReceived($order);

        $this->assertStringContainsString('Hemos recibido tu solicitud personalizada', $mailable->envelope()->subject);
        $this->assertStringContainsString('#'.$order->id, $mailable->envelope()->subject);
        $mailable->assertSeeInHtml('#'.$order->id);
    }

    /** @test */
    public function new_order_admin_notification_mailable_content()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_QUOTATION]);
        $mailable = new NewOrderAdminNotification($order);

        $this->assertStringContainsString('Nueva Solicitud de Cotización', $mailable->envelope()->subject);
        $this->assertStringContainsString('#'.$order->id, $mailable->envelope()->subject);

        $orderPaid = Order::factory()->create(['status' => Order::STATUS_PAID]);
        $mailablePaid = new NewOrderAdminNotification($orderPaid);
        $this->assertStringContainsString('Pago Recibido', $mailablePaid->envelope()->subject);
    }

    /** @test */
    public function order_paid_notification_mailable_content()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_PAID]);
        $mailable = new OrderPaidNotification($order);

        $this->assertStringContainsString('Confirmación de Pago', $mailable->envelope()->subject);
        $this->assertStringContainsString('#'.$order->id, $mailable->envelope()->subject);
    }

    /** @test */
    public function order_shipped_notification_mailable_content()
    {
        $order = Order::factory()->create(['status' => Order::STATUS_SHIPPED]);
        $mailable = new OrderShippedNotification($order);

        $this->assertStringContainsString('Tu pedido ha sido enviado', $mailable->envelope()->subject);
        $this->assertStringContainsString('#'.$order->id, $mailable->envelope()->subject);
    }

    /** @test */
    public function price_assigned_notification_mailable_content()
    {
        $order = Order::factory()->create(['type' => Order::TYPE_CUSTOM, 'total_amount' => 100]);
        $mailable = new PriceAssignedNotification($order);

        $this->assertStringContainsString('Cotización Lista', $mailable->envelope()->subject);
        $this->assertStringContainsString('#'.$order->id, $mailable->envelope()->subject);
    }
}
