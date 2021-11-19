<?php

namespace Database\Seeders;

use App\Models\SocialProfileType;
use Illuminate\Database\Seeder;

class ProfileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SocialProfileType::create(['profile_type'=>'Instagram']);
        SocialProfileType::create(['profile_type'=>'Apple Music']);
        SocialProfileType::create(['profile_type'=>'Twitter']);
        SocialProfileType::create(['profile_type'=>'Soundcloud']);
        SocialProfileType::create(['profile_type'=>'Facebook']);
        SocialProfileType::create(['profile_type'=>'Google Play']);
        SocialProfileType::create(['profile_type'=>'Spotify']);
        SocialProfileType::create(['profile_type'=>'Tidal']);
        SocialProfileType::create(['profile_type'=>'Wikipedia']);
    }
}
