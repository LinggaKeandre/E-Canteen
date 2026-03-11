<?php

namespace Tests\Unit;

use App\Models\Order;
use PHPUnit\Framework\TestCase;

class OrderCancellationTest extends TestCase
{
    /**
     * Verify canRequestCancel only returns true when status is 'pending' and
     * no cancel request/completion flags are set.
     */
    public function test_can_request_cancel_only_pending()
    {
        $order = new Order();
        $order->status = 'pending';
        $order->cancel_request = 'none';
        $order->is_completed = false;
        $order->is_auto_confirmed = false;
        $this->assertTrue($order->canRequestCancel());

        // preparations and other statuses should be disallowed
        $order->status = 'preparing';
        $this->assertFalse($order->canRequestCancel());

        $order->status = 'ready';
        $this->assertFalse($order->canRequestCancel());

        // already completed
        $order->status = 'pending';
        $order->is_completed = true;
        $this->assertFalse($order->canRequestCancel());

        // cancel request pending
        $order->is_completed = false;
        $order->cancel_request = 'pending';
        $this->assertFalse($order->canRequestCancel());

        // canceled already
        $order->cancel_request = 'accepted';
        $this->assertFalse($order->canRequestCancel());
    }
}
