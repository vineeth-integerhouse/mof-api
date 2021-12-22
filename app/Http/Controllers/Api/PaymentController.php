<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CardDetail;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;

use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /* Add Payment */

    public function stripe(Request $request)
    {
        $data = [];
        $validate_data = Validator::make($request->all(), [
            'name_on_card'  => 'required',
            'credit_card_number'  => 'required',
            'country_region'  => 'required',
            'zip_code'  => 'required',
            'amount' => 'required',
            'stripe_token' => 'required',
            'currency' => 'required',
            'payee_id' => 'required',
            'payin_payout'=> 'required',
        ]);

        if ($validate_data->fails()) {
            $errors = $validate_data->errors();
            $message =  implode(', ', $errors->all());
            $status_code = BADREQUEST;
        } else {
            $current_user = get_user();
            Stripe::setApiKey(config('stripe.stripe_secret'));
            $stripe_data = Customer::create([
                "source" => $request->stripe_token,
            ]);
            
    
            $card_data=[];
            $card_data['user_id'] = $current_user->id;
            $card_data['name_on_card'] = $request->name_on_card;
            $card_data['credit_card_number'] = $request->credit_card_number;
            $card_data['country_region'] = $request->country_region;
            $card_data['zip_code'] = $request->zip_code;

            $inserted_data=CardDetail::updateOrCreate($card_data);

            $payment_data = [];
            $payment_data['payer'] = $current_user->id;
            $payment_data['payee'] = $request->payee_id;
            $payment_data['name'] = $inserted_data->name_on_card;
            $payment_data['amount'] = $request->amount;
            $payment_data['payment_date'] = date("Y/m/d");
            $payment_data['payment_method'] = $current_user->payment_method;
            $payment_data['stripe_reference_number'] = $stripe_data->id;
            $payment_data['card_detail_id'] = $inserted_data->id;
            $payment_data['payin_payout'] =  $request->payin_payout;
          

          $payment_inserted_data=  Payment::create($payment_data);
            $data['Payment']=Payment::select(
                'id',
                'payer',
                'payee',
                'card_detail_id' ,
                'name',
                'payment_date',
                'amount',
                'stripe_reference_number',
                'status',
                'payin_payout'
            )->where('id',$payment_inserted_data->id)->get()->first();

            $data['Card Details']=CardDetail::select(
                'id',
                'user_id',
                'name_on_card',
                'credit_card_number',
                'country_region',
                'zip_code',
            )->where('id',$inserted_data->id)->get()->first();

            $status_code = SUCCESSCODE;
            $message = __('user.payment');
        }

        return response([
            'data' => $data,
            'stripe'=>$stripe_data,
            'message' => $message,
            'status_code' => $status_code
        ]);
    }
    
    /* List Payments */
    public function list(Request $request)
    {
        $data = [];
        $message = __('user.fetch_payment failed');
        $status_code = BADREQUEST;
        $current_user = get_user();
        $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
        $sort_column = !empty($request->input('sort_column')) ? $request->input('sort_column') : "created_at";
        $sort_direction = !empty($request->input('sort_direction')) ? $request->input('sort_direction')  : "desc";
 
        $page = (!empty($request->input('page')) && $request->input('page') > 0) ? intval($request->input('page')) : 1;
        $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;
 
        $payment = Payment::select(
            'id',
            'name',
            'amount',
            'payment_date',
            'status',
            'payment_method',
            'payer',
            'payee',
        )->where('payee', $current_user->id)->orderBy(DB::raw('payments.'.$sort_column), $sort_direction)->paginate($limit, $offset);
 
        if (isset($payment)) {
            $data = $payment;
            $payment_data['Total Revenue'] = Payment::select('amount')->get()->sum('amount');
            $message = __('user.fetch_payment_success');
            $status_code = SUCCESSCODE;
        }
        return response([
             'data' => $data,
             'payment_data' => $payment_data,
             'message' => $message,
             'status_code' => $status_code
         ], $status_code);
    }

    /*Admin List Payments */
    public function payment_list(Request $request)
    {
        $data = [];
        $message = __('user.fetch_payment failed');
        $status_code = BADREQUEST;
  
        $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
        $sort_column = !empty($request->input('sort_column')) ? $request->input('sort_column') : "created_at";
        $sort_direction = !empty($request->input('sort_direction')) ? $request->input('sort_direction')  : "desc";
  
        $page = (!empty($request->input('page')) && $request->input('page') > 0) ? intval($request->input('page')) : 1;
        $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;
  
        $payment = Payment::select(
            'payments.id',
            'payments.name',
            'payment_date',
            'amount',
            'stripe_reference_number',
            'status',
            'payer',
            'payee',
            'profile_pic',
            'payin_payout',
            'username'
        )->leftJoin('users', 'users.id', '=', 'payments.payer')
        ->where(DB::raw('payments.payin_payout'), '=', 'Payin')
        ->orderBy(DB::raw('payments.'.$sort_column), $sort_direction)->paginate($limit, $offset);
  
        if (isset($payment)) {
            $data = $payment;
            $payment_data['Total Revenue'] = Payment::select('amount')->where('payin_payout','Payin')->get()->sum('amount');

            $total_payout=Payment::select('amount')->where('payin_payout','Payouts')->get()->sum('amount');
            $payment_data['Total Profit'] =  $payment_data['Total Revenue'] - $total_payout;

            $message = __('user.fetch_payment_success');
            $status_code = SUCCESSCODE;
        }
        return response([
              'data' => $data,
              'payment_data' => $payment_data,
              'message' => $message,
              'status_code' => $status_code
          ], $status_code);
    }


    /*Admin fetch Payments */
    public function payment_fetch(Request $request,$payment_id)
    {
        $data = [];
        $message = __('user.fetch_payment failed');
        $status_code = BADREQUEST;
  
        $payment = Payment::withTrashed()->select(
            'payments.id',
            'payments.name',
            'payment_date',
            'amount',
            'stripe_reference_number',
            'status',
            'payer',
            'payee',
            'profile_pic',
            'payin_payout',
            'username'
        )->leftJoin('users', 'users.id', '=', 'payments.payer')
        ->where(DB::raw('payments.payin_payout'), '=', 'Payin')
        ->where(DB::raw('payments.id'),$payment_id)
       ->first();
  
        if (isset($payment)) {
            $data = $payment;
    
            $message = __('user.fetch_payment_success');
            $status_code = SUCCESSCODE;
        }
        return response([
              'data' => $data,
              'message' => $message,
              'status_code' => $status_code
          ], $status_code);
    }

     /*Admin List Payouts */
     public function payout_list(Request $request)
     {
         $data = [];
         $message = __('user.fetch_payment failed');
         $status_code = BADREQUEST;
   
         $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
         $sort_column = !empty($request->input('sort_column')) ? $request->input('sort_column') : "created_at";
         $sort_direction = !empty($request->input('sort_direction')) ? $request->input('sort_direction')  : "desc";
   
         $page = (!empty($request->input('page')) && $request->input('page') > 0) ? intval($request->input('page')) : 1;
         $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;
   
         $payment = Payment::select(
             'payments.id',
             'users.name',
             'payment_date',
             'amount',
             'stripe_reference_number',
             'status',
             'payer',
             'payee',
             'profile_pic',
             'payin_payout',
             'username'
         )->leftJoin('users', 'users.id', '=', 'payments.payee')
         ->where(DB::raw('payments.payin_payout'), '=', 'Payouts')
         ->orderBy(DB::raw('payments.'.$sort_column), $sort_direction)->paginate($limit, $offset);
   
         if (isset($payment)) {
             $data = $payment;
             $message = __('user.fetch_payment_success');
             $status_code = SUCCESSCODE;
         }
         return response([
               'data' => $data,
               'message' => $message,
               'status_code' => $status_code
           ], $status_code);
     }

      /*Admin Fetch Payouts */
      public function payout_fetch(Request $request,$payout_id)
      {
          $data = [];
          $message = __('user.fetch_payment failed');
          $status_code = BADREQUEST;
    
          $payment = Payment::select(
              'payments.id',
              'users.name',
              'payment_date',
              'amount',
              'stripe_reference_number',
              'status',
              'payer',
              'payee',
              'profile_pic',
              'payin_payout',
              'username'
          )->leftJoin('users', 'users.id', '=', 'payments.payee')
          ->where(DB::raw('payments.payin_payout'), '=', 'Payouts')
          ->where(DB::raw('payments.id'),$payout_id)
          ->first();
     
    
          if (isset($payment)) {
              $data = $payment;
              $message = __('user.fetch_payment_success');
              $status_code = SUCCESSCODE;
          }
          return response([
                'data' => $data,
                'message' => $message,
                'status_code' => $status_code
            ], $status_code);
      }
  
 
 

    public function update(Request $request)
    {
        $data=[];
        $current_user = get_user();

        $validate_data = Validator::make($request->all(), [
            'status' => 'required',
        ]);
        
        if ($validate_data->fails()) {
            $errors = $validate_data->errors();
            $message =  implode(', ', $errors->all());
            $status_code = BADREQUEST;
        } else {
            if ($current_user) {
                try {
                    DB::table('payments')->where('payer', $current_user->id)->update([
                        'status' => $request->status ?? $current_user->status
                    ]);
                } catch (\Illuminate\Database\QueryException  $e) {
                    $message = __('user.payment_status') + $e;
                    $status_code = BADREQUEST;
                }
            }
            $data = Payment::where('payer', $current_user->id)->first();

            $message = __('user.payment_status');
            $status_code = SUCCESSCODE;
        }

        return response([
            'data' => $data,
            'message' => $message,
            'status_code' => $status_code,
        ]);
    

    }
}
