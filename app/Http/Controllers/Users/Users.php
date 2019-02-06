<?php

namespace App\Http\Controllers\Users;

use Illuminate\Http\Request;
// use Illuminate\Session;
use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\Buyer;
use App\Models\Admin;
use Hash;

class Users extends Controller
{
    public function loginUser(Request $request){   
        $email = $request->email;
        $password = $request->password;
        $buyer = Buyer::where("email", $email)->get();
        $seller = Seller::where("email", $email)->where('status', 1)->get();
        $admin = Admin::where("email", $email)->get();
        $type = null;
        $login_id = null;
        $responseErr = response()->json(array(["message" => "Email/Password is Incorrect"]), 201);

        if (!count($buyer) && !count($seller) && !count($admin)) {
            return $responseErr;
        }

        if (count($buyer) > 0 && Hash::check($password, $buyer[0]["password"])) {
            $login_id = $buyer[0]["buyer_id"];
            $type = "buyer";
        } else if (count($seller) > 0 && Hash::check($password, $seller[0]["password"])) {
            $login_id = $seller[0]["seller_id"];
            $type = "seller";
        } else if (count($admin) > 0 && $admin[0]["password"] === $password) {
            $login_id = $admin[0]["admin_id"];
            $type = "admin";
        }

        if (empty($type)){
            return $responseErr;
        }

        return response()->json(array(["email" => $email, "login_id" => $login_id, "type" => $type, "message" => "You are now logged in"]), 200);
    }

}
