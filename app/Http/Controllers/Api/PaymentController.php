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
        ]);

        if ($validate_data->fails()) {
            $errors = $validate_data->errors();
            $message =  implode(', ', $errors->all());
            $status_code = BADREQUEST;
        } else {
            $current_user = get_user();
            Stripe::setApiKey(config('stripe.stripe_secret'));
            $data = Customer::create([
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
            $payment_data['stripe_reference_number'] = $data->id;
            $payment_data['card_detail_id'] = $inserted_data->id;
          

            Payment::create($payment_data);
            $status_code = SUCCESSCODE;
            $message = __('user.payment');
        }

        return response([
            'data' => $data,
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
            $data['Total Revenue'] = Payment::select('amount')->get()->sum('amount');
            $message = __('user.fetch_payment_success');
            $status_code = SUCCESSCODE;
        }
        return response([
             'data' => $data,
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
            'id',
            'name',
            'payment_date',
            'amount',
            'stripe_reference_number',
            'status',
            'payer',
            'payee',
        )->orderBy(DB::raw('payments.'.$sort_column), $sort_direction)->paginate($limit, $offset);
  
        if (isset($payment)) {
            $data = $payment;
            $data['Total Revenue'] = Payment::select('amount')->get()->sum('amount');
            $message = __('user.fetch_payment_success');
            $status_code = SUCCESSCODE;
        }
        return response([
              'data' => $data,
              'message' => $message,
              'status_code' => $status_code
          ], $status_code);
    }
}
