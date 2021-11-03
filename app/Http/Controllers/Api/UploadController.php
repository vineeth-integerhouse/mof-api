<?php


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $data = [];
        $message = '';
        $status_code = '';
        $google_storage_url='';

        $validate_data = Validator::make($request->all(), [
            'file' => 'required |mimes:jpg,jpeg,png,bmp,tiff,mp4'
        ]);

        if ($validate_data->fails()) {
            $errors = $validate_data->errors();
            $message =  implode(', ', $errors->all());
            $status_code = BADREQUEST;
        } else {
            $file=$request->file;
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
        
             $google_storage_url = 'https://storage.googleapi.com/' . $storage_bucket_name. '/'. $full_file_name;
            $message=__('user.upload_success');
            $status_code= SUCCESSCODE;
        }

        return response([
            'data' => $google_storage_url,
            'message' => $message,
            'status_code' => $status_code
        ], $status_code);
    }
}
