<?php

namespace Database\Seeders;

use App\Models\SubscriptionType;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubscriptionType::create(['subscription_type'=>'Monthly']);
        SubscriptionType::create(['subscription_type'=>'Weekly']);
    }
}
