<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Customer;

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
}
