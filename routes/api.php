<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::group(['prefix' => 'api', 'middleware' => 'cors'], function(){
//   //USERS
//   Route::post('/loginUser',  'Users\Users@loginUser');
//   Route::post('/checkUserIfLogin',  'Users\Users@checkUserIfLogin');
  
//   //BUYERS
//   Route::post('/signUpBuyer',  'Users\Buyers@signUpBuyer');

//   //SELLERS
//   Route::post('/checkIfEmailSent',  'Users\Sellers@checkIfEmailSent');
//   Route::post('/verifyEmail',  'Users\Sellers@verifyEmail');
//   Route::post('/signUpSeller',  'Users\Sellers@signUpSeller');
//   Route::post('/checkIfCodeExist',  'Users\Sellers@checkIfCodeExist');

//   //PRODUCTS
//   Route::post('/addProduct',  'Products\Products@addProduct');
//   Route::get('/getProductsOfSpecificSeller/{email}',  'Products\Products@getProductsOfSpecificSeller');
//   Route::get('/getProductDetails/{id}',  'Products\Products@getProductDetails');
//   Route::get('/getProductsOfAllSellers',  'Products\Products@getProductsOfAllSellers');
//   Route::post('/cartOrderSummary',  'Products\Products@cartOrderSummary');
//   Route::get('/getOrdersSummary/{id}',  'Products\Products@getOrdersSummary');
// });


