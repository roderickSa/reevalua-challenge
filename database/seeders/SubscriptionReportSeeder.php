<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\SubscriptionReport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriptions = Subscription::all();

        foreach ($subscriptions as $subscription) {
            SubscriptionReport::create([
                'subscription_id' => $subscription->id,
                'year' => 2025,
                'month' => 12,
            ]);
        }
    }
}
