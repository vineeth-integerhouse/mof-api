<?php

use Google\Auth\Credentials\GCECredentials;
use Illuminate\Support\Facades\Auth;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Storage;

function get_user()
{
    return Auth::user();
}
