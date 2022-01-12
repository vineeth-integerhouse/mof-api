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
        SocialProfileType::create(['profile_type'=>'Instagram','profile_img'=> 'https://storage.googleapis.com/mof_user_files/profile_image/instagram.svg']);
        SocialProfileType::create(['profile_type'=>'Apple Music','profile_img'=> 'https://storage.googleapis.com/mof_user_files/profile_image/applemusic.svg']);
        SocialProfileType::create(['profile_type'=>'Twitter','profile_img'=> 'https://storage.googleapis.com/mof_user_files/profile_image/twitter.svg']);
        SocialProfileType::create(['profile_type'=>'Soundcloud','profile_img'=> 'https://storage.googleapis.com/mof_user_files/profile_image/soundcloud.svg']);
        SocialProfileType::create(['profile_type'=>'Facebook','profile_img'=> 'https://storage.googleapis.com/mof_user_files/profile_image/facebook.svg']);
        SocialProfileType::create(['profile_type'=>'Google Play','profile_img'=>'https://storage.googleapis.com/mof_user_files/profile_image/googleplay.svg']);
        SocialProfileType::create(['profile_type'=>'Spotify','profile_img'=> 'https://storage.googleapis.com/mof_user_files/profile_image/spotify.svg']);
        SocialProfileType::create(['profile_type'=>'Tidal','profile_img'=>'https://storage.googleapis.com/mof_user_files/profile_image/tidal.svg']);
        SocialProfileType::create(['profile_type'=>'Wikipedia','profile_img'=>'https://storage.googleapis.com/mof_user_files/profile_image/wikipedia.svg']);
    }
}
