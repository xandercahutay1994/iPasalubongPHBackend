<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\Buyer;
use App\Models\Cart;
use App\Models\Feedback;
use App\Models\Products;
use App\Models\Copy;
use DB;

class Buyers extends Controller
{
    public function signUpBuyer(Request $request){
        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $email = $request->email;
        $password = $request->password;
        $phone = $request->phone;
        
        if (!$firstname || !$lastname || !$email || !$password || !$phone) {
            return response()->json("All fields are required");
        }
        
        $registeredBuyer = Buyer::select('email')->where('email',$email)->get();
        if (count($registeredBuyer) > 0) {
            return response()->json("Email already exist", 201);
        }

        $buyer = new Buyer;
        $buyer->firstname = $firstname;
        $buyer->lastname = $lastname;
        $buyer->email = $email;
        $buyer->password = Hash::make($password);
        $buyer->phone = $phone;
        if (!$buyer->save()) {
            return response()->json("Error saving data", 500);
        }
        return response()->json("Save successfully", 200);
    }

    public function buyerCheckoutDetails(Request $request, $buyer_id) {
        $buyerDetails = Buyer::where("buyer_id", $buyer_id)->get();
        $details = Cart::select('*')
                    ->join('products', 'carts.product_id', 'products.product_id')
                    ->where('buyer_id', $buyer_id)
                    ->where("carts.status", 0)
                    ->get();
        $total = 0;

        foreach ($details as $key => $value) {
            $temp =  $value->orderQuantity * $value->price;
            $total += $temp;
        }

        return response()->json(array("details" => $details, "buyer" => $buyerDetails, "total" => $total));
    }

    public function getBuyerOrders(Request $request, $buyer_id) {
        // $orders = Cart::select('products.image', 'products.name', 'products.price', 'carts.orderQuantity')
        //         ->join('products', 'carts.product_id', 'products.product_id')
        //         ->join('sellers', 'products.seller_id', 'sellers.seller_id')
        //         ->where('carts.buyer_id', $buyer_id)
        //         ->get();

        $orders = Cart::select('products.image', 'products.name', 'products.price', 'carts.orderQuantity',
                'sellers.shopName', 'carts.status', 'products.product_id')
                ->join('products', 'carts.product_id', 'products.product_id')
                ->join('sellers', 'sellers.seller_id', 'products.seller_id')
                ->where('buyer_id', $buyer_id)
                ->where('carts.status', 2)
                ->orWhere('carts.status', 3)
                ->get();

        return response()->json($orders);
    }


    public function checkIfBuyerOrderedAProduct(Request $request) {
        $buyer_id = $request->buyer_id;
        $product_id = $request->product_id;
        $details = Cart::where('buyer_id', $buyer_id)->where('product_id', $product_id)->where('status', 3)->get();

        return response()->json($details);
    }

    public function getAllReviewsOfAProduct(Request $request, $product_id) {
        $details = Feedback::where('product_id', $product_id)->get();

        return response()->json($details);
    }

    public function updateBuyerStatusWhenPaid(Request $request) {
        $reference_number = $request->reference_number;
        $seller_id = $request->seller_id;

        $data = Copy::select('*')
                ->join('products', 'products.seller_id', 'copy_delivery.seller_id')
                // ->join('carts', 'carts.product_id', 'products.product_id')      
                ->join('delivery', 'delivery.delivery_id', 'copy_delivery.copy_delivery_id')
                ->where('reference_number', $reference_number)
                ->get();

        // $details = DB::table('copy_delivery')
        //     ->where('reference_number', $reference_number)
        //     ->update([
        //         'reference_number' => ''
        //     ]);

        $products = Cart::select('*')->get();

        foreach ($products as $key => $product) {
            foreach ($data as $key => $value) {
                if ($value->product_id == $product->product_id) {
                // return response()->json($value->buyer_id);
                    
                    DB::table('carts')
                        ->where('product_id', $value->product_id)
                        // ->where('buyer_id', $value->buyer_id)
                        ->update([
                            'status' => '3'
                        ]);
                }
            }
        }

        $copy = Copy::select('*')
                    ->join('delivery', 'delivery.delivery_id', 'copy_delivery.delivery_id')
                    ->join('buyers', 'buyers.buyer_id', 'delivery.buyer_id')
                    ->where('seller_id', $seller_id)
                    ->where('reference_number', '!=', '')
                    ->get();

        return response()->json($copy);
    }
}
