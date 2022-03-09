<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TourController extends Controller
{
    /* Add Tour*/
    public function add(Request $request)
    {
        $data = [];
      
        $message = __('user.add_tour_failed');
        $status_code = BADREQUEST;

        $current_user = get_user();
  
        $data['date'] = $request->date;
        $data['time'] = date("H:i:s", strtotime($request->time));
        $data['venue'] = $request->venue;
        $data['city'] = $request->city;
        $data['ticket_link'] = $request->ticket_link;
        $data['user_id'] = $current_user->id;
           
        $inserted_data = Tour::create($data);

        $tour= DB::table('tours')
        ->select(
            'id',
            'date',
            DB::raw("DATE_FORMAT(tours.time, '%h:%i %p') as time"),
            'venue',
            'city',
            'ticket_link',
            'user_id',
        )->where('id', $inserted_data->id)->get()->first();
        $message = __('user.add_tour_success');
        $status_code = SUCCESSCODE;
         
        return response([
           'data' => $tour,
           'message' => $message,
           'status_code' => $status_code,
       ], $status_code);
    }

    /* Edit Tour */
    public function update(Request $request, $tour_id)
    {
        $data = [];
        $message     =  '';
        $status_code = '';
  
        $tour_data = Tour::find($tour_id);

        $current_user = get_user();
     
        if ($tour_data) {
            try {
                $update = [];
                if (isset($request->date)) {
                    $update['date'] = $request->date;
                    ;
                }
                if (isset($request->time)) {
                    $update['time'] = date("H:i:s", strtotime($request->time));
                }
                if (isset($request->venue)) {
                    $update['venue'] = $request->venue;
                }
                if (isset($request->city)) {
                    $update['city'] = $request->city;
                }
                if (isset($request->ticket_link)) {
                    $update['ticket_link'] = $request->ticket_link;
                }

                $update['updated_at'] = date("Y-m-d H:i:s");

                if (count($update) != 0) {
                    DB::table('tours')->where('id', $tour_id)->where('user_id', $current_user->id)->update($update);
                }
                $data= DB::table('tours')
                        ->select(
                            'id',
                            'date',
                            DB::raw("DATE_FORMAT(tours.time, '%h:%i %p') as time"),
                            'venue',
                            'city',
                            'ticket_link',
                            'user_id',
                        )->where('id', $tour_id)->where('user_id', $current_user->id)->get()->first();
                $message = __('user.update_success');
                $status_code = SUCCESSCODE;
            } catch (Exception $e) {
                $data=[];
                $message = __('user.update_failed') . ' ' . $e->getMessage();
                $status_code = BADREQUEST;
            }
        }

        return response([
             'data'        => $data,
             'message'     => $message,
             'status_code' => $status_code
         ], $status_code);
    }

    /* Fetch Tour*/
    public function get(Request $request, $artist_id)
    {
        $message =  __('user.tour_fetch failed');
        $status_code = BADREQUEST;
 
        $data= DB::table('tours')
         ->select(
             'id',
             'date',
             DB::raw("DATE_FORMAT(tours.time, '%h:%i %p') as time"),
             'venue',
             'city',
             'ticket_link',
             'deleted_at'
         )->where('user_id', $artist_id)->get();
 
        if (isset($data)) {
            $message = __('user.tour_fetch_success');
            $status_code = SUCCESSCODE;
        }
 
        return response([
             'data'        => $data,
             'message'     => $message,
             'status_code' => $status_code
         ], $status_code);
    }

    /* List Tour */
    public function list(Request $request)
    {
        $data = [];
        $message = __('user.tour_fetch failed');
        $status_code = BADREQUEST;
 
        $tour = DB::table('tours')
         ->select(
             'id',
             'date',
             DB::raw("DATE_FORMAT(tours.time, '%h:%i %p') as time"),
             'venue',
             'city',
             'ticket_link',
             'user_id',
         )->where('deleted_at', '=', Null)->get();

        if (isset($tour)) {
            $message = __('user.tour_fetch_success');
            $status_code = SUCCESSCODE;
            $data = $tour;
        }
        return response([
             'data' => $data,
             'message' => $message,
             'status_code' => $status_code
         ], $status_code);
    }


    /* Delete Tour*/
    public function delete(Request $request, $tour_id)
    {
        $data      = [];
        $message =  __('user.invalid_user');
        $status_code = BADREQUEST;

        $current_user = get_user();

        $tour = Tour::where('id', $tour_id)->first();

        $tour_data = Tour::where('id', $tour_id)->where('user_id', $current_user->id)->delete();
       
        if ($tour_data === 1) {
            $data['id']   = $tour->id;
            $message = __('user.tour_delete_success');
            $status_code = SUCCESSCODE;
        } else {
            $message = __('user.tour_delete_failed');
            $status_code = BADREQUEST;
        }
    
        return response([
            'data'        => $data,
            'message'     => $message,
            'status_code' => $status_code
        ], $status_code);
    }

        /* Admin Delete Tour*/
        public function admin_delete(Request $request, $tour_id, $artist_id)
        {
            $data      = [];
            $message =  __('user.invalid_user');
            $status_code = BADREQUEST;
    
    
            $tour = Tour::where('id', $tour_id)->first();
    
            $tour_data = Tour::where('id', $tour_id)->where('user_id', $artist_id)->delete();
           
            if ($tour_data === 1) {
                $data['id']   = $tour->id;
                $message = __('user.tour_delete_success');
                $status_code = SUCCESSCODE;
            } else {
                $message = __('user.tour_delete_failed');
                $status_code = BADREQUEST;
            }
        
            return response([
                'data'        => $data,
                'message'     => $message,
                'status_code' => $status_code
            ], $status_code);
        }

     /* Admin Edit Tour */
     public function admin_update(Request $request, $artist_id,$tour_id)
     {
         $data = [];
         $message     =  '';
         $status_code = '';
   
         $tour_data = Tour::find($tour_id);
 
    
         if ($tour_data) {
             try {
                 $update = [];
                 if (isset($request->date)) {
                     $update['date'] = $request->date;
                     ;
                 }
                 if (isset($request->time)) {
                     $update['time'] = date("H:i:s", strtotime($request->time));
                 }
                 if (isset($request->venue)) {
                     $update['venue'] = $request->venue;
                 }
                 if (isset($request->city)) {
                     $update['city'] = $request->city;
                 }
                 if (isset($request->ticket_link)) {
                     $update['ticket_link'] = $request->ticket_link;
                 }
 
                 $update['updated_at'] = date("Y-m-d H:i:s");
 
                 if (count($update) != 0) {
                     DB::table('tours')->where('id', $tour_id)->where('user_id',$artist_id)->update($update);
                 }
                 $data= DB::table('tours')
                         ->select(
                             'id',
                             'date',
                             DB::raw("DATE_FORMAT(tours.time, '%h:%i %p') as time"),
                             'venue',
                             'city',
                             'ticket_link',
                             'user_id',
                         )->where('id', $tour_id)->where('user_id',$artist_id)->get()->first();
                 $message = __('user.update_success');
                 $status_code = SUCCESSCODE;
             } catch (Exception $e) {
                 $data=[];
                 $message = __('user.update_failed') . ' ' . $e->getMessage();
                 $status_code = BADREQUEST;
             }
         }
 
         return response([
              'data'        => $data,
              'message'     => $message,
              'status_code' => $status_code
          ], $status_code);
     }
}
