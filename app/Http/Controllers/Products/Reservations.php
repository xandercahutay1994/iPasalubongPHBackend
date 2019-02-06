<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Cart;
use App\Models\Buyer;
use App\Models\Product;
use DB;
class Reservations extends Controller
{
    public function reserveProductByBuyer(Request $request) {
        $product_id = $request->product_id;
        $carts = Cart::where('product_id', $product_id);
        $buyer_id = $request->buyer_id;

        $reserved = new Reservation;
        $reserved->buyer_id = $buyer_id;
        $reserved->product_id = $product_id;
        $reserved->reservation_date = $request->reservation_date;

        if ($reserved->save()) {
            $cart = DB::table('carts')
                ->where('product_id', $product_id)
                ->update([
                    'status' => '5'
                ]);

            $buyerDetails = Buyer::where("buyer_id", $buyer_id)->get();
            $details = Cart::select('*')
                        ->join('products', 'carts.product_id', 'products.product_id')
                        ->where('buyer_id', $buyer_id)
                        ->where("carts.status", 0)
                        ->get();
            $total = 0;
            $quantity = 0;

            foreach ($details as $key => $value) {
                $temp =  $value->orderQuantity * $value->price;
                $total += $temp;
                $quantity += $value->orderQuantity;
            }

            return response()->json(array("message" => 'Product successfully moved to reservation', "details" => $details, "buyer" => $buyerDetails, "total" => $total, "quantity" => $quantity));
        }
    }

    public function getReservationDetails(Request $request, $buyer_id) {
        $carts = Cart::where('buyer_id', $buyer_id)->get();
        $quantity = array();
        
        foreach ($carts as $key => $value) {
            array_push($quantity, $value->orderQuantity);
        }

        $details = Reservation::select('*')
                ->join('products', 'products.product_id', 'reservations.product_id')
                ->join('carts', 'carts.product_id', 'reservations.product_id')
                ->where('reservations.buyer_id', $buyer_id)
                ->get();
                
        return response()->json(array('details' => $details, 'quantity' => $quantity));
    }

    public function searchReservationDetails(Request $request) {
        $advance_search = $request->advance_search;
        $dataSearch = Cart::where('carts.buyer_id', $request->buyer_id)
                ->join('products', 'carts.product_id', 'products.product_id')
                ->join('reservations', 'carts.product_id', 'reservations.product_id')
                ->where('carts.status', 5)
                ->where('name', 'LIKE', '%' . $advance_search . '%')              
                ->orWhere('category', 'LIKE', '%' . $advance_search . '%')              
                ->get();

        return response()->json($dataSearch);
    }

    public function deleteReservation(Request $request) {
        $buyer_id = $request->buyer_id;
        $product_id = $request->product_id;

        $deleteReservation = DB::table('reservations')
                    ->where('buyer_id', $buyer_id)
                    ->where('product_id', $product_id)
                    ->delete();

        $deleteCart = DB::table('carts')
                    ->where('buyer_id', $buyer_id)
                    ->where('product_id', $product_id)
                    ->where('status', '5')
                    ->delete();

        return response()->json($this->getReservationDetails($request, $buyer_id));
    }

    public function moveToCart(Request $request) {
        $buyer_id = $request->buyer_id; 
        $orderQuantity = $request->orderQuantity;
        $product_id = $request->product_id;

        $updateQty = DB::table('carts')
                ->where('product_id', $product_id)
                ->where('status', 5)
                ->update([
                    'status' => 0
                ]);

        if ($updateQty) {
            $deleteReservation = DB::table('reservations')
                ->where('reservation_id', $request->reservation_id)
                ->delete();
        }

        $reservations = Reservation::select('*')
                ->join('products', 'products.product_id', 'reservations.product_id')
                ->join('carts', 'carts.product_id', 'reservations.product_id')
                ->where('reservations.buyer_id', $buyer_id)
                ->get();
                
        $carts = Cart::select('*')
                    ->join('products', 'carts.product_id', 'products.product_id')
                    ->where('buyer_id', $buyer_id)
                    ->where("carts.status", 0)
                    ->get();

        $quantity = 0;

        foreach ($carts as $key => $value) {
            $quantity += $value->orderQuantity;
        }

        return response()->json(array('reservations' => $reservations, 'quantity' => $quantity));
    }
}
