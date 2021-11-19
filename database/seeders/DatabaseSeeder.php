<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(GenreSeeder::class);
        $this->call(PostTypeSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(SubscriptionSeeder::class);
        $this->call(WhoCanSeePostSeeder::class);
        $this->call(WhenToPostSeeder::class);
        $this->call(NotificationTypeSeeder::class);  
        $this->call(ProfileTypeSeeder::class);  
        
    }
}
