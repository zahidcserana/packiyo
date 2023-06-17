<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\SubscriptionPrice;
use Illuminate\Console\Command;
use Laravel\Cashier\Cashier;
use Stripe\Exception\ApiErrorException;

class SyncPackiyoSubscriptionPlans extends Command
{
    protected $signature = 'sync:subscriptions';

    protected $description = 'Sync subscription plans from Stripe to our database';

    /**
     * @throws ApiErrorException
     */
    public function handle(): void
    {
        try {
            $products = Cashier::stripe()->products->all();
            $prices = Cashier::stripe()->prices->all();

            $this->info('Syncing stripe products and prices to DB...');

            foreach ($products as $product) {
                Plan::firstOrCreate([
                    'stripe_id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description
                ]);
            }

            foreach ($prices as $price) {
                SubscriptionPrice::firstOrCreate([
                    'stripe_id' => $price->id,
                    'stripe_product_id' => $price->product,
                    'plan_id' => Plan::whereStripeId($price->product)->first()->id
                ]);
            }

            $this->info('Syncing completed!');
        } catch (\Exception $exception) {
            $this->error('Could not sync because of the following exception: ');
            $this->error($exception->getMessage());
        }
    }
}
