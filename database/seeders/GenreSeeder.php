<?php

namespace Database\Seeders;

use App\Models\GenreType;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GenreType::create(['Genre_option'=>'Rock']);
        GenreType::create(['Genre_option'=>'R&B']);
        GenreType::create(['Genre_option'=>'Hip Hop']);
        GenreType::create(['Genre_option'=>'Pop']);
        GenreType::create(['Genre_option'=>'Folk']);
        GenreType::create(['Genre_option'=>'Country']);
        GenreType::create(['Genre_option'=>'Rap']);
        GenreType::create(['Genre_option'=>'Soul']);
        GenreType::create(['Genre_option'=>'Jazz']);
        GenreType::create(['Genre_option'=>'Indie Pop']);
        GenreType::create(['Genre_option'=>'Alternative Rock']);
        GenreType::create(['Genre_option'=>'Heavy Metal']);
        GenreType::create(['Genre_option'=>'Reggae']);
        GenreType::create(['Genre_option'=>'Techno']);
        GenreType::create(['Genre_option'=>'Soul']);
        GenreType::create(['Genre_option'=>'Electronic Dance Music']);

    }
}
