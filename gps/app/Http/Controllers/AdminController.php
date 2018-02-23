<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use App\Item;
use App\Vehicle;
use App\Client;
use App\Command;
use App\VehicleItem;
use Braintree;

class AdminController extends Controller
{
    /*
            Route::post('item', 'AdminController@MakeItem');
            Route::post('truck', 'AdminController@MakeTruck');
            Route::post('client', 'AdminController@MakeClient');
            Route::post('createCommand', 'AdminController@MakeCommand');
            Route::post('createSession', 'AdminController@MakeSession');
            Route::post('createSupplier', 'AdminController@MakeSupplier');
            
            Route::post('stopSession', 'AdminController@StopSession');
            
            Route::post('pay', 'BrainTreeController@pay');
            
            Route::post('addItemToCommand', 'AdminController@AddItemToSession');
            Route::post('addCommandToSession', 'AdminController@AddCommandToSession');
     */
    
    public function MakeItem(Request $req) {
        $data = $req->validate([
            'name' => 'required|string',
            'conditioning' => 'required|boolean',
            'amountPerPackaging' => 'required|integer',
        ]);
        Item::create($data);
        return Response::json($data, 201);
    }
    public function MakeTruck(Request $req) {
        $data = $req->validate([
            'licence' => 'required|integer|in:1,5',
            'conditioning' => 'required|boolean',
            'capacity' => 'required|integer',
        ]);
        Vehicle::create($data);
        return Response::json($data, 201);
    }
    public function MakeClient(Request $req) {
        $data = $req->validate([
            'name' => 'required|string',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);
        Client::create($data);
        return Response::json($data, 201);
    }
    public function MakeCommand(Request $req) {
        $data = $req->validate([
            'clientId' => 'required|integer|exists:clients,id',
        ]);
        Command::create($data);
        return Response::json($data, 201);
    }
    
    /*
            Route::get('pay', 'AdminController@Pay');
            Route::post('finishTransaction', 'AdminController@FinishTransaction');
    */
    
    private function configureBraintree() {
        Braintree\Configuration::environment('sandbox');
        Braintree\Configuration::merchantId(env('BRAINTREE_MERCHANT_ID'));
        Braintree\Configuration::publicKey(env('BRAINTREE_PUBLIC_KEY'));
        Braintree\Configuration::privateKey(env('BRAINTREE_PRIVATE_KEY'));
        
    }
    
    public function Pay(Request $req) {
        $this->configureBraintree();
        return view('braintree/pay', ['braintree_key' => Braintree\ClientToken::generate()]);
    }
    
    public function FinishTransaction(Request $req) {
        error_log("ayyy");
        $this->configureBraintree();
        $nonce = $req->post('payment_methode_nonce');
        error_log(json_encode($nonce));
        if (isset($nonce)) {
            error_log("in");
            $gateway = new Braintree\Gateway(array(
                'accessToken' => useYourAccessToken,
            ));
            error_log("in2");
            
            //access_token$production$nrt2qychw46ygbwq$f1c8c32d2df785fe683be5fe9e3ed76a
            $result = $gateway->transaction()->sale([
                "amount" => $_POST['amount'],
                'merchantAccountId' => 'CAD',
                "paymentMethodNonce" => $_POST['payment_method_nonce'],
                "orderId" => $_POST['Mapped to PayPal Invoice Number'],
                "descriptor" => [
                    "name" => "Descriptor displayed in customer CC statements. 22 char max"
                ],
                "shipping" => [
                    "firstName" => "Jen",
                    "lastName" => "Smith",
                    "company" => "Braintree",
                    "streetAddress" => "1 E 1st St",
                    "extendedAddress" => "Suite 403",
                    "locality" => "Bartlett",
                    "region" => "IL",
                    "postalCode" => "60103",
                    "countryCodeAlpha2" => "US"
                ],
                "options" => [
                    "paypal" => [
                        "customField" => $_POST["PayPal custom field"],
                        "description" => $_POST["Description for PayPal email receipt"]
                    ],
                ]
            ]);
            
            error_log("in3");
            if ($result->success) {
                print_r("Success ID: " . $result->transaction->id);
            } else {
                print_r("Error Message: " . $result->message);
            }
            /*$result = Braintree\Transaction::sale([
                'amount' => '10.00',
                'paymentMethodNonce' => $nonce,
                'options' => [
                    'submitForSettlement' => true
                ]
            ]);
            if ($result->success) {
                return 'payment done';
            } else {
                return 'you wot mate';
            }*/
        }
        return 'ayy';
    }
    
    
    
    public function EditCommand(Request $req) {
        // TODO
    }
}
