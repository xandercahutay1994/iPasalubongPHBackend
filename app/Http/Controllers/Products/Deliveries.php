<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Product;
use App\Models\Copy;
use App\Models\Cart;
use App\Models\Buyer;
use App\Models\Seller;
use App\Mail\SendReferenceNumber;
use DB;

class Deliveries extends Controller
{
    public function createDeliveryCheckout(Request $request) {
        set_time_limit(0);

        $buyer_id = $request->buyer_id;
        $totalPayment = $request->totalPayment;
        $city = $request->city;
        $province = $request->province;
        $address = $request->address;
        $date = $request->date;
        $zip_code = $request->zip_code;
        $payment_type = $request->payment_type;
        $details = $request->details;

        $referenceNumber = str_random(20);
        
        $deliver = new Delivery;
        $deliver->buyer_id = $buyer_id;
        $deliver->total_payment = $totalPayment;
        $deliver->city = $city;
        $deliver->province = $province;
        $deliver->address = $address;
        $deliver->date = $date;
        $deliver->zip_code = $zip_code;
        $deliver->reference_number = $referenceNumber;
        $deliver->payment_type = $payment_type;

        if ($deliver->save()) {
            $updateCart = DB::table("carts")
                ->where("buyer_id", $buyer_id)
                ->update([
                    "status" => 2
                ]);
            
            $products = Product::select('*')->get();
    
            foreach ($products as $key => $value) {
                foreach ($details as $key => $detail) {
                    if ($detail['product_id'] === $value->product_id) {
                        DB::table('products')
                        ->where('product_id', $detail['product_id'])
                        ->update([
                            'quantity' => $value->quantity - $detail['orderQuantity'],
                            'orderCounter' => $value->orderCounter + 1
                        ]); 
                    }
                }
            }

            foreach ($details as $key => $value) {
                $clone = new Copy;
                $clone->seller_id = $value['seller_id'];
                // $clone->buyer_id = $deliver['buyer_id'];
                $clone->delivery_id = $deliver['id'];
                $clone->reference_number = $referenceNumber;
                $clone->save();
            }

            $email = Buyer::where('buyer_id', $buyer_id)->get(); 
            
            if ($payment_type === 'coins') {
                \Mail::to($email[0]->email)->send(new SendReferenceNumber($email[0]->email,$referenceNumber, $totalPayment));
            }

            return response()->json(array("deliver" => $deliver, "message" => "Created succesfully"));
        }
    }

    public function getAllDeliveries(Request $request, $seller_id) {
        $details = Seller::select('*')
                ->join('products', 'products.seller_id', 'sellers.seller_id')
                ->join('carts', 'carts.product_id', 'products.product_id')
                ->join('buyers', 'buyers.buyer_id', 'carts.cart_id')
                ->join('delivery', 'delivery.buyer_id', 'carts.buyer_id')
                ->where('carts.status', 3)
                ->where('sellers.seller_id', $seller_id)
                ->get();

        return response()->json($details);
    }
}
