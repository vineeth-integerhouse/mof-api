<?php

namespace Database\Seeders;

use App\Models\WhoCanSeePost;
use Illuminate\Database\Seeder;

class WhoCanSeePostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WhoCanSeePost::create(['whocanseepost_option'=>'Everyone']);
        WhoCanSeePost::create(['whocanseepost_option'=>'Fans only']);
        WhoCanSeePost::create(['whocanseepost_option'=>'Only people you tag']);
    }   
}
