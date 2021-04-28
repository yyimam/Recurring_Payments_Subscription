<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Braintree;
use DateTime;

class BraintreeController extends Controller
{
    protected $customer_id = '219699205';

    // Make a connection to Braintree
    public function gateway()
    {
        // in config func. services.braintree values are coming from config/services.php
        $gateway = new Braintree\Gateway([
            'environment' => config('services.braintree.environment'),
            'merchantId' => config('services.braintree.merchantId'),
            'publicKey' => config('services.braintree.publicKey'),
            'privateKey' => config('services.braintree.privateKey')
        ]);

        return $gateway;
    }

    // Generates Token for the client-side and return it with view(front-end) 
    public function client_token_and_view($view,$values = null)
    {
        $gateway = $this->gateway();
        $token = $gateway->ClientToken()->generate();
        $array_of_values = ['token' => $token];
        if (isset($values)) {            
            $array_of_values += $values;
        }

        return view($view, $array_of_values);
        // return $array_of_values;
    }

    public function success($success, $message = "")
    {
        return view('successful',['is_successful' => $success, 'message' => $message]);
    }

    public function select_plan()
    {
        $gateway = $this->gateway();
        $plans = $gateway->plan()->all();

        
        try {
            $customer = $gateway->customer()->find($this->customer_id);
        } catch (\Throwable $th) {
            return $this->success(false,"Customer Not Found");
        }

        // checking if user is subscribed or not
        $is_subscribed = false;
        if (isset( $customer->creditCards[0]->subscriptions[0]->status))
        {
            foreach($customer->creditCards as $creditcard)
            { 
                foreach($creditcard->subscriptions as $subscription)
                {
                    if ($subscription->status == "Active") {
                        $is_subscribed = true;
                    }
                }
            }  
        }
        
        if ($is_subscribed) {
            return $this->success(false,"Already Subscribed To A Plan");
        }
        else{
            return view('selectPlan', ['plans' => $plans, 'is_subscribed' => $is_subscribed]);
        }
    }

    public function simple_transaction_view(Type $var = null)
    {
        return $this->client_token_and_view('simpleTransaction');
    }

    public function simple_transaction(Request $request)
    {     
        $amount = $request->amount;
        $nonce = $request->payment_method_nonce;

        $gateway = $this->gateway();
        $result = $gateway->transaction()->sale([
            'amount' => $amount,
            'paymentMethodNonce' => $nonce,
            'options' => [
                'submitForSettlement' => true
            ]
        ]);

        if ($result->success) {
            $transaction = $result->transaction;
            return back()->with('success_message', 'Transaction Successful of ID: ' . $transaction->id);
        } else {
            $errorString = "";
            foreach($result->errors->deepAll() as $error) {
                $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
            }
            return back()->withErrors('An error occurred with a message: ' . $result->message);
        }
    }

    public function subscribe(Request $request)
    {
        $gateway = $this->gateway();

        try {
            $customer = $gateway->customer()->find($this->customer_id);
        } catch (\Throwable $th) {
            return $this->success(false,"Customer Not Found");
        }

        $payment_method_token = $customer->paymentMethods[0]->token;

        $is_successful = true;
        try {
            $result = $gateway->subscription()->create([
                'paymentMethodToken' => $payment_method_token,
                'planId' => $request->plan_id
            ]);
        } catch (\Throwable $th) {
            $is_successful = false;
        }

        return $this->success($is_successful);
    }

    public function manage_subscription(){

        $gateway = $this->gateway();

        // fetching plan's name
        $plan_names = [];
        $plans = $gateway->plan()->all();
        foreach ($plans as $plan) {
            $plan_names [$plan->id] =  $plan->name;
        }

        try {
            $customer = $gateway->customer()->find($this->customer_id);
        } catch (\Throwable $th) {
            return $this->success(false,"Customer Not Found");
        }

        $customer_plans = [];
        if (isset($customer->creditCards[0]->subscriptions[0]->status))
        {
            foreach($customer->creditCards as $creditcard)
            { 
                foreach($creditcard->subscriptions as $subscription)
                {
                    // if ($subscription->status != "Canceled") {
                        array_push($customer_plans,$subscription);
                    // }
                }
            }  
            // return $customer->creditCards[0]->subscriptions[0]->planId;
            // return $plans;
            // return $customer_plans[0];  
            return view('managePlan',['customer_plans' => $customer_plans, "plan_names" => $plan_names]);
        }
        else{
            return $this->success(false,"No Plan Subscribed .");
        }

    }

    public function cancel_subscription(Request $request)
    {
        $gateway = $this->gateway();
        try {
            $result = $gateway->subscription()->cancel($request->subscription_id);
        } catch (\Throwable $th) {
            return $this->success(false,"Invalid Subscription ID.");
        }

        return $this->success(true,'Subscription Cancelled Successfully');
    }

    public function filter_report()
    {
        $gateway = $this->gateway();
        $plans = $gateway->plan()->all();

        return view('reportFilters',['plans' => $plans]);

    }

    public function generate_report(Request $request)
    {
        // return $request->all();
        $gateway = $this->gateway();

        // fetching plan's name
        $plan_names = [];
        $plans = $gateway->plan()->all();
        foreach ($plans as $plan) {
            $plan_names [$plan->id] =  $plan->name;
        }

        if (isset($request->status)) {
            $status_array = [$request->status];
        }
        else{
            $status_array = [
                "Active",
                "Canceled",
                "Past Due",
                "Pending",
                "Expired"
            ];
        }
        
        // fetching subscription And filtering
        if (isset($request->from_date) && isset($request->to_date)) {
            $searchResults = $gateway->subscription()->search([
                Braintree\SubscriptionSearch::planId()->contains($request->planId),
                Braintree\SubscriptionSearch::status()->in($status_array),
                Braintree\SubscriptionSearch::createdAt()->between(
                  new DateTime($request->from_date),
                  new DateTime($request->to_date)
                ),
              ]);
        }
        elseif (isset($request->from_date)) {
            $searchResults = $gateway->subscription()->search([
                Braintree\SubscriptionSearch::planId()->contains($request->planId),
                Braintree\SubscriptionSearch::status()->in($status_array),
                Braintree\SubscriptionSearch::createdAt()->between(
                  new DateTime($request->from_date),
                  new DateTime()
                )
              ]);
        }
        else{
            $searchResults = $gateway->subscription()->search([
                Braintree\SubscriptionSearch::planId()->contains($request->planId),
                Braintree\SubscriptionSearch::status()->in($status_array),
            ]);
        }


        // fetching customer's name and email
        $customer_details = [];
        foreach ($searchResults as $item) {
            // return $item;
            $customer_collection = $gateway->customer()->search([
                Braintree\CustomerSearch::paymentMethodToken()->is($item->paymentMethodToken)
                ]);
            foreach ($customer_collection as $cc) {
                $customer_details[$item->paymentMethodToken] ['name'] = $cc->firstName . " " . $cc->lastName;
                $customer_details[$item->paymentMethodToken] ['email'] = $cc->email;
                if ( isset($request->userName) && strpos($customer_details[$item->paymentMethodToken]['name'], $request->userName) === false) {
                    $customer_details[$item->paymentMethodToken] ['visiblity'] = false;
                }
                else{
                    $customer_details[$item->paymentMethodToken] ['visiblity'] = true;
                }
            }
        }

        return view('report',['report_data' => $searchResults, 'plan_names' => $plan_names, 'customer_details' => $customer_details]);
        // return view('report');
    }

}
