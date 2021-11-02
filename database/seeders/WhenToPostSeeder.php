<?php

namespace Database\Seeders;

use App\Models\WhenToPost;
use Illuminate\Database\Seeder;

class WhenToPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WhenToPost::create(['whentopost_option'=>'Publish now']);
        WhenToPost::create(['whentopost_option'=>'Schedule']);
        WhenToPost::create(['whentopost_option'=>'Save as draft']);
    }
}
