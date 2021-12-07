<?php


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    /* Upload */
    
    public function upload(Request $request)
    {
        $data = [];
        $message = '';
        $status_code = 200;
        $google_storage_url='';

        $validate_data = Validator::make($request->all(), [
            'file' => 'required |mimes:jpg,jpeg,png,bmp,tiff,mp4',
            
        ]);
             
        if ($request->hasfile('file')) {
            foreach ($request->file('file') as $file) {
                $full_file_name = time() . '_' . $file->getClientOriginalName();
                   
                $storage = new StorageClient([
                'projectId' => config('googlecloud.project_id'),
                'driver' => 'gcs',
                'key' => config('googlecloud.credentials'),
             ]);
        
                $storage_bucket_name = config('googlecloud.storage_bucket');
                $file = fopen($file, 'r');

                $bucket = $storage->bucket($storage_bucket_name);
                $object = $bucket->upload($file, [
                'name' =>  $full_file_name
             ]);
        
                $google_storage_url = 'https://storage.googleapis.com/' . $storage_bucket_name. '/'. $full_file_name;
                array_push($data, $google_storage_url);
            }
            
            $message=__('user.upload_success');
            $status_code= SUCCESSCODE;
        }

        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }
}
