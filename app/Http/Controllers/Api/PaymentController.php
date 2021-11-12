<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;

use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
     public function stripe(Request $request)
    {
        $data = [];
        $validate_data = Validator::make($request->all(), [
            'amount' => 'required',
            'stripe_token' => 'required',
            'currency' => 'required',
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

            $payment_data = [];
            $payment_data['user_id'] = $current_user->id;
            $payment_data['name'] = $current_user->name;
            $payment_data['amount'] = $request->amount;
            $payment_data['payment_date'] = date("Y/m/d");
            $payment_data['payment_method'] = $request->payment_method;
            $payment_data['stripe_reference_number'] = $data->id;
          

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
    
    // listing payments
    public function fetch_payment(Request $request)
    {
        $data = [];
         $message = __('user.fetch_payment failed');
         $status_code = BADREQUEST;
 
 
         $limit = !empty($request->input('limit')) ? $request->input('limit') : 10;
         $sort_column = !empty($request->input('sort_column')) ? $request->input('sort_column') : "created_at";
         $sort_direction = !empty($request->input('sort_direction')) ? $request->input('sort_direction')  : "desc";
 
         $page = (!empty($request->input('page')) && $request->input('page') > 0) ? intval($request->input('page')) : 1;
         $offset = ($page > 1) ? ($limit * ($page - 1)) : 0;
 
         $payment = Payment::
         select(
            'id',
            'name',
            'amount',
            'payment_date',
            'payment_method',
            'stripe_reference_number',
            'user_id',
         )->orderBy(DB::raw('payments.'.$sort_column), $sort_direction)->paginate($limit, $offset);
 
         if (isset($payment)) {
            $message = __('user.fetch_payment_success');
             $status_code = SUCCESSCODE;
             $data = $payment;
         }
         return response([
             'data' => $data,
             'message' => $message,
             'status_code' => $status_code
         ], $status_code);
    }
}
