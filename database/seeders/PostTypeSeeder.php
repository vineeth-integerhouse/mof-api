<?php

namespace Database\Seeders;

use App\Models\PostType;
use Illuminate\Database\Seeder;

class PostTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PostType::create(['post_option'=>'Post']);
        PostType::create(['post_option'=>'Image']);
        PostType::create(['post_option'=>'Video']);
        PostType::create(['post_option'=>'Audio']);
        PostType::create(['post_option'=>'Live Stream']);
    }
}
