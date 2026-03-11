<?php

namespace App\Console\Commands;

use App\Http\Controllers\User\CartController;
use App\Models\Order;
use Illuminate\Console\Command;

class AutoConfirmOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-confirm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-confirm orders that have been ready for more than 3 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for orders that need auto-confirmation...');
        
        $orders = Order::where('status', 'ready')
            ->where('is_confirmed_by_seller', true)
            ->where('is_confirmed_by_user', false)
            ->where('is_completed', false)
            ->where('is_auto_confirmed', false)
            ->whereNotNull('ready_at')
            ->get();
        
        $count = 0;
        
        foreach ($orders as $order) {
            if ($order->canAutoConfirm()) {
                $cartController = new CartController();
                if ($cartController->autoConfirmOrder($order)) {
                    $count++;
                    $this->info("Order #{$order->id} auto-confirmed and transferred to seller.");
                }
            }
        }
        
        $this->info("Auto-confirmed {$count} orders.");
        
        return Command::SUCCESS;
    }
}
