<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\BraintreeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Simple Transaction
Route::get('simple-transaction',[BraintreeController::class,'simple_transaction_view']);
Route::post('simple-transaction',[BraintreeController::class,'simple_transaction']);

// Subscription
Route::prefix('subscription')->group(function ()
{
    Route::get('/',[BraintreeController::class,'select_plan']);
    Route::post('/',[BraintreeController::class,'subscribe']);
    Route::get('/manage',[BraintreeController::class,'manage_subscription']);
    Route::delete('/cancel',[BraintreeController::class,'cancel_subscription']);
    Route::get('/report',[BraintreeController::class,'filter_report']);
    Route::post('/report',[BraintreeController::class,'generate_report']);
});



// Route::view('/simple-transaction', 'simpleTransaction');

// Route::get('simple-transaction', function () {

//     // in config func. services.braintree values are coming from config/services.php
//     $gateway = new Braintree\Gateway([
//         'environment' => config('services.braintree.environment'),
//         'merchantId' => config('services.braintree.merchantId'),
//         'publicKey' => config('services.braintree.publicKey'),
//         'privateKey' => config('services.braintree.privateKey')
//     ]);

//     $token = $gateway->ClientToken()->generate();

//     return view('simpleTransaction', [
//         'token' => $token
//     ]);
// });

// Route::post('simple-transaction/checkout', function (Request $request) {

//     // in config func. services.braintree values are coming from config/services.php
//     $gateway = new Braintree\Gateway([
//         'environment' => config('services.braintree.environment'),
//         'merchantId' => config('services.braintree.merchantId'),
//         'publicKey' => config('services.braintree.publicKey'),
//         'privateKey' => config('services.braintree.privateKey')
//     ]);

//     $amount = $request->amount;
//     $nonce = $request->payment_method_nonce;

//     $result = $gateway->transaction()->sale([
//         'amount' => $amount,
//         'paymentMethodNonce' => $nonce,
//         'options' => [
//             'submitForSettlement' => true
//         ]
//     ]);

//     if ($result->success) {
//         $transaction = $result->transaction;
//         // header("Location: " . $baseUrl . "transaction.php?id=" . $transaction->id);
//         return back()->with('success_message', 'Transaction Successful of ID: ' . $transaction->id);
//     } else {
//         $errorString = "";

//         foreach($result->errors->deepAll() as $error) {
//             $errorString .= 'Error: ' . $error->code . ": " . $error->message . "\n";
//         }

//         // $_SESSION["errors"] = $errorString;
//         // header("Location: " . $baseUrl . "index.php");
//         return back()->withErrors('An error occurred with a message: ' . $result->message);
//     }

//     // return view('simpleTransaction', [
//     //     'token' => $token
//     // ]);
// });

