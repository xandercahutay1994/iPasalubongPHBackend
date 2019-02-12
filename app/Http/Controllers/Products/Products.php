<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Seller;
use App\Models\Cart;
use Illuminate\Support\Facades\Response;
use Session;
use DB;

class Products extends Controller
{
    public function addProduct(Request $request) {
        $email = $request->email;
        $imageRequest = $request->get('img_url');
        $product_name = $request->product_name;
        $product_price = $request->product_price;
        $quantity = $request->quantity;
        $product_category = $request->product_category;
        $product_description = $request->product_description;

        if (!($imageRequest && $product_name && $product_price && $quantity  && $product_category && $product_description)) {
            return response()->json("All fields are required!", 201);
        }

        $seller = Seller::where("email", $email)->get();
        $sellerId = $seller[0]['seller_id'];
        $ranNumber = str_random(5);
        $product_image = time() . '-' . $ranNumber  .'-' . explode('/', 
            explode(':', substr($imageRequest, 0, strpos($imageRequest, ';')))[1])[1];

        try {
            $product = new Product;
            $product->name = $product_name;
            $product->seller_id = $sellerId;
            $product->price = $product_price;
            $product->image = $product_image;
            $product->quantity = $quantity;
            $product->category = $product_category;
            $product->description = $product_description;

            if ($product->save()){
                \Image::make($imageRequest)->save(public_path('storage/productImages/') . $product_image);
                return response()->json('Product successfully added!', 200);
            }
        } catch(\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getProductsOfSpecificSeller(Request $request, $seller_id) {
        $products = Product::where("seller_id", $seller_id)->where('status', 0)->get();
        return response()->json($products);
    }

    public function getProductDetails(Request $request, $id) {
        $product = Seller::select('*')
                ->join('products', 'products.seller_id', 'sellers.seller_id')
                // ->where('quantity', '>', 0)
                ->where('product_id', $id)
                ->get();
        return response()->json($product);
    }

    public function getProductsOfAllSellers(Request $request) {
        $products = Product::select('*')->where('quantity', '>', 0)->where('status', 0)->get();
        return response()->json(array(["products" => $products]), 200);
    }

    public function cartOrderSummary(Request $request) {
        $buyer_id = $request->buyer_id; 
        $orderQuantity = $request->orderQuantity;
        $product_id = $request->product_id;

        $isExist = false;
        $prevQty = 0;
        $totalQty = 0;
        $cartLists = Cart::where('buyer_id', $buyer_id)->where('status', 0)->get(); 
        $cartData = null;
        foreach ($cartLists as $key => $value) {
            $totalQty += $value->orderQuantity;
            if ($value->product_id === $product_id) {
                $cartData = $value;
                $prevQty += $value->orderQuantity;
                $isExist = true;
            }
        }

        if ($isExist) {
            $updateQty = DB::table('carts')
                        ->where('product_id', $product_id)
                        ->where('status', 0)
                        ->update([
                            'orderQuantity' => $prevQty + $orderQuantity
                        ]);
            // return response()->json(array("quantity" => $prevQty + $orderQuantity), 200);
            return response()->json(array("quantity" => $totalQty + $orderQuantity), 200);
        } 

        $cart =  new Cart;
        $cart->buyer_id = $buyer_id;
        $cart->orderQuantity= $orderQuantity;
        $cart->product_id = $product_id;
        
        if ($cart->save()) {
            return response()->json(array("quantity" => $totalQty + $orderQuantity), 200);
            // return response()->json($cart);
        }
    }

    public function getOrdersSummary(Request $request, $buyer_id) {
        $cartData = Cart::where("buyer_id", $buyer_id)->get();
        return response()->json($cartData);
    }

    public function removeProductToCart(Request $request) {
        $cart_id = $request->cart_id;
        $product_delete = DB::table('carts')
                        ->where('cart_id',  $cart_id)
                        ->delete();

        $cart = Cart::select('*')->where('status', 0)->get();
        $remainingOrder = 0;
        foreach ($cart as $key => $value) {
            $remainingOrder += $value->orderQuantity;
        }
        
        if (!$product_delete) {
            return response()->json(array(["message" => "Error deleting product in cart"]), 200);
        }
        return response()->json(array(["message" => "Product has been removed to cart", "list" => $cart, "remainingOrder" => $remainingOrder]), 200);
    }

    public function getInventoryReportOfSeller(Request $request, $seller_id) {
        $details = Product::where('seller_id', $seller_id)->get();
        return response()->json($details);
    }

    public function searchByProductName(Request $request) {
        $advance_search = $request->advance_search;
        $dataSearch = Product::select('*')
                    ->where('name', 'LIKE', '%' . $advance_search . '%')
                    ->where('status', 0)
                    ->where('seller_id', $request->seller_id)
                    ->get();
        return response()->json($dataSearch);
    }

    public function updateProduct(Request $request) {
        $product = Product::where('product_id', $request->product_id)->get();

        $updateProduct = DB::table('products')
                  ->where('product_id', $request->product_id)
                  ->update([
                      'name' => $request->name,
                      'price' => $request->price,
                      'quantity' => $product[0]['quantity'] + $request->quantity,
                      'category' => $request->category,
                      'description' => $request->description
                  ]);
        
        if (!$updateProduct) {
            return response()->json('Error in updating the product');    
        }

        $updatedProducts = Product::where('seller_id', $request->seller_id)->where('status', 0)->get();
        return response()->json($updatedProducts);
    }

    public function deactivateProduct(Request $request) {
        $product_id = $request->product_id;

        $deactivate = DB::table('products')
                    ->where('product_id', $product_id)
                    ->update([
                        'status' => 1
                    ]);

        if (!$deactivate) {
            return response()->json('Error in deactivating the product');    
        }

        $updatedProducts = Product::where('seller_id', $request->seller_id)->where('status', '0')->get();
        return response()->json($updatedProducts);
    }

    public function activateProduct(Request $request) {
        $product_id = $request->product_id;

        $deactivate = DB::table('products')
                    ->where('product_id', $product_id)
                    ->update([
                        'status' => 0
                    ]);

        if (!$deactivate) {
            return response()->json('Error in activating the product');    
        }

        $updatedProducts = Product::where('seller_id', $request->seller_id)->where('status', '1')->get();
        return response()->json($updatedProducts);
    }

    public function getDeactivateProducts(Request $request, $seller_id) {
        $products = Product::where('seller_id', $seller_id)
                    ->where('status', 1)
                    ->get();

        return response()->json($products);
    }

    public function searchByCategory(Request $request) {
        $category = strtolower($request->type);
        $name = strtolower($request->name);
        
        $data = Product::where('category', $category)
                        ->where('name', 'LIKE', '%' . $name . '%')
                        ->where('description', 'LIKE', '%' . $name . '%')
                        ->get();

        return response()->json($data);
        // $dataSearch = Product::where('seller_id', $request->seller_id)
        // ->where('name', 'LIKE', '%' . $advance_search . '%')
        // ->get();

    }

    public function searchByPlaces(Request $request) {
        $searchType = $request->searchType;
        $advance_search = $request->advance_search;
        
        if ($searchType === 'product') {
            $searchCategory = Seller::select('*')
                    ->join('products', 'sellers.seller_id', 'products.seller_id')
                    ->where('name', 'LIKE', '%' . $advance_search . '%')
                    ->get();

            return response()->json($searchCategory);
        }

        if ($searchType === 'place') {
            $searchPlaces = Seller::select('*')
                    ->join('products', 'sellers.seller_id', 'products.seller_id')
                    ->where('shopAddress', 'LIKE', '%' . $advance_search . '%')
                    ->get();

            return response()->json($searchPlaces);
        }

        if ($searchType === 'price') {
            $searchPrices = Seller::select('*')
                    ->join('products', 'sellers.seller_id', 'products.seller_id')
                    ->where('price', 'LIKE', '%' . $advance_search . '%')
                    ->get();

            return response()->json($searchPrices);
        }

        if ($searchType === 'shop') {
            $searchShops = Seller::select('*')
                    // ->join('products', 'sellers.seller_id', 'products.seller_id')
                    ->where('shopName', 'LIKE', '%' . $advance_search . '%')
                    ->get();

            return response()->json($searchShops);
        }

        if ($searchType === 'category') {
            $searchCategory = Seller::select('*')
                    ->join('products', 'sellers.seller_id', 'products.seller_id')
                    ->where('products.status', 0)
                    ->where('category', 'LIKE', '%' . $advance_search . '%')
                    ->get();

            return response()->json($searchCategory);
        }
    }

}
