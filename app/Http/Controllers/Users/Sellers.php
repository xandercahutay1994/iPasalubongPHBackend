<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\Cart;
use App\Models\Delivery;
use App\Models\SellerToken;
use App\Models\Copy;
use App\Mail\Verification;
use App\Mail\Activation;
use App\Mail\SellerSignUp;
use DB;
use Hash;

class Sellers extends Controller
{
    public function verifyEmail(Request $request) {
        set_time_limit(0);
        
        try{
            $email = $request->email;
            $code = str_random(32);
            // $mail = \Mail::to($email)->send(new Verification($email,$code));

            // if (is_object($mail)) {
            //     return response()->json('Email failed to send!');
            // }

            $emailExists = SellerToken::where("email", $email)->get();
            if (count($emailExists) === 0) {
                $token = new SellerToken;
                $token->email = $email;
                $token->code = $code;
                $token->save();
                return response()->json(array(["email" => $email]), 200);
            }
            $updateCode = DB::table('token')
                            ->where('email', $email)
                            ->update([
                                'code' => $code
                            ]);
            return response()->json(array(["email" => $emailExists[0]['email']]), 200);

        }catch(Exception $e){
            return response()->json($e->getMessage(), 500);
        }
    }

    public function checkIfEmailSent(Request $request) {
        $tokenStatus = SellerToken::where("email", $request->email)->get();
        if (count($tokenStatus) === 0) {
            return response()->json("Email doesn't exist",201);            
        }
        return response()->json(array(["hasToken" => true]));
    }

    public function checkIfCodeExist(Request $request) {
        $email = $request->email;
        $code = $request->code;

        $data = SellerToken::where("email", $email)->get();        
        if (count($data) === 0 || $data[0]['code'] !== $code) {
            return response()->json('Code not exist',201);
        }
        
        return response()->json(array("sellerCode" => $data[0]['code']));
    }

    public function signUpSeller(Request $request){
        set_time_limit(0);
        
        $imageRequest = $request->get('img_url');
        $registeredSeller = Seller::select('email')->where('email',$request->email)->get();
        if(count($registeredSeller) > 0){
            return response()->json("Email already exist", 201);
        }

        if (!$imageRequest) {
            return response()->json("Image is required", 201);
        }

        try {
            $ranNum = str_random(5);
            $image = time() . '-' . $ranNum  .'.' . explode('/', 
                explode(':', substr($imageRequest, 0, strpos($imageRequest, ';')))[1])[1];
    
            $buyer = new Seller;
            $buyer->shopName = $request->shopName;
            $buyer->shopAddress = $request->shopAddress;
            $buyer->image = $image;
            $buyer->email = $request->email;
            $buyer->password = Hash::make($request->password);
            $buyer->phone = $request->phone;
            $buyer->token = str_random(32);
            
            if ($buyer->save()) {
                // \Mail::to($request->email)->send(new SellerSignUp($request->email));
                \Image::make($imageRequest)->save(public_path('storage/sellerDTIImages/') . $image);
                return response()->json("Save successfully", 200);
            }
        } catch(\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getUnverifiedSellers(Request $request) {    
        // $sellers = Seller::select('*')->where('status', 0)->where('token', '!=', '')->get();
        $sellers = Seller::select('*')->where('token', '!=', '')->get();
        return response()->json(array("sellers" => $sellers));
    }


    public function getAllSellers(Request $request) {
        $sellers = Seller::select('*')->where('token', '')->get();
        return response()->json(array("sellers" => $sellers));
    }

    public function verifySeller(Request $reqbuest) {
        $seller_id = $request->seller_id;
        $updateStatus = DB::table('sellers')
                        ->where('seller_id', $seller_id)
                        ->update([
                            'status' => 1
                        ]);
                            
        return response()->json($seller_id);
    }

    public function getDeliveryOrders(Request $request, $seller_id) {
        $details = Copy::select('*')
                ->join('delivery', 'delivery.delivery_id', 'copy_delivery.delivery_id')
                ->join('buyers', 'buyers.buyer_id', 'delivery.buyer_id')
                ->where('seller_id', $seller_id)
                ->where('reference_number', '!=', '')
                ->get();

        $orders = Delivery::select('*')
                ->join('carts', 'carts.buyer_id', 'delivery.buyer_id')
                ->join('products', 'products.product_id', 'carts.product_id')
                ->join('copy_delivery', 'copy_delivery.seller_id', 'products.seller_id')
                ->where('copy_delivery.seller_id', $seller_id)
                ->where('carts.status', 2)
                ->get();

        // $copy = array();
        // foreach ($orders as $key => $value) {
        //     $details = array(
        //             'firstname' => $value->firstname, 'lastname' => $value->lastname,
        //             'total_payment' => $value->total_payment, 'payment_type' => $value->payment_type
        //         );
        //     array_push($copy, $details);
        // }
            
        return response()->json(array('orders' => $orders, 'details' => $details));
    }

    public function getDeliveryLists(Request $request) {
        // $delivers = Delivery::select('*')
        //         ->join('buyers', 'delivery.buyer_id', 'buyers.buyer_id')
        //         ->join('carts', '')
    }

    public function activateSeller(Request $request) {
        $activate = DB::table('sellers')
                    ->where('seller_id', $request->seller_id)
                    ->update([
                        'status' => 1
                    ]);

        $details = Seller::where('seller_id', $request->seller_id)->get();
        // \Mail::to($details[0]['email'])->send(new Activation($details[0]['email'], 'ac'));

        // $sellers = Seller::where('status', 1)->get();
        $sellers = Seller::where('token', '')->get();
        return response()->json($sellers);
    }

    public function deactivateSeller(Request $request) {
        $deactivate = DB::table('sellers')
                    ->where('seller_id', $request->seller_id)
                    ->update([
                        'status' => 0
                    ]);

        $details = Seller::where('seller_id', $request->seller_id)->get();
        // \Mail::to($details[0]['email'])->send(new Activation($details[0]['email'], 'de-ac'));
            
        // $sellers = Seller::where('status', 0)->get();
        $sellers = Seller::where('token', '')->get();
        return response()->json($sellers);
    }

    public function updateSellerIfPaid(Request $request) {
        $details = DB::table('sellers')
                ->where('shopName', $request->shopName)
                ->update([
                    'token' => ''
                ]);

        $sellers = Seller::where('token', '!=', '')->get();
        return response()->json($sellers);
    }

    public function searchPaidUnpaidSeller(Request $request) {
        $type = $request->type; 
        $search = $request->advance_search;

        if ($type === 'paid') {
            $dataSearch = Seller::select('*')
                        ->where('token', '=', '')
                        ->where('shopName', 'LIKE', '%' . $search . '%')
                        // ->where('shopAddress', 'LIKE', '%' . $search . '%')
                        ->get();

            return response()->json(array('type' => $type, 'data' => $dataSearch));
        } else {
            $dataSearch = Seller::select('*')
                        ->where('token', '!=', '')
                        ->where('shopName', 'LIKE', '%' . $search . '%')
                        // ->orWhere('shopAddress', 'LIKE', '%' . $search . '%')
                        // ->orWhere('email', 'LIKE', '%' . $search . '%')
                        ->get();

            return response()->json(array('type' => $type, 'data' => $dataSearch));
        }
    }

    public function listsOfSellers(Request $request) {
        $details = Seller::where('status', 1)->get();

        return response()->json($details);
    }
}
