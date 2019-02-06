<?php

namespace App\Http\Controllers\Products;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Feedback;
use DB;

class Feedbacks extends Controller
{
    public function giveFeedbacks(Request $request) {
        $feedback = $request->feedback;
        $rate = $request->rate;
        
        if (!$request->feedback_rating_id) {
            $feed = new Feedback;
            $feed->feedback = $feedback;
            $feed->rate = $rate;
            $feed->product_id = $request->product_id;
            $feed->buyer_id = $request->buyer_id;
            $feed->save();
        } else {
            DB::table('feedback_rating')
                ->where('feedback_rating_id', $request->feedback_rating_id)
                ->update([
                    'feedback' => $feedback,
                    'rate' => $rate
                ]);
        }

        $details = Feedback::select('*')
                ->join('buyers', 'feedback_rating.buyer_id', 'buyers.buyer_id')
                ->where('product_id', $request->product_id)
                ->get();

        return response()->json($details);
    }

    public function getAllFeedbackByProduct(Request $request, $product_id) {
        $details = Feedback::select('*')
                ->join('buyers', 'feedback_rating.buyer_id', 'buyers.buyer_id')
                ->where('product_id', $product_id)
                ->get();

        return response()->json($details);
    }
}
