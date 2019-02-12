<?php

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


Route::group(['prefix' => 'api', 'middleware' => 'cors'], function(){
  //USERS
  Route::post('/loginUser',  'Users\Users@loginUser');
  
  //BUYERS
  Route::post('/signUpBuyer',  'Users\Buyers@signUpBuyer');
  Route::post('/checkIfBuyerOrderedAProduct',  'Users\Buyers@checkIfBuyerOrderedAProduct');
  Route::post('/updateBuyerStatusWhenPaid',  'Users\Buyers@updateBuyerStatusWhenPaid');
  Route::post('/updateCheckoutDetails',  'Users\Buyers@updateCheckoutDetails');
  
  Route::get('/buyerCheckoutDetails/{buyer_id}',  'Users\Buyers@buyerCheckoutDetails');
  Route::get('/getBuyerOrders/{buyer_id}',  'Users\Buyers@getBuyerOrders');
  Route::get('/getAllReviewsOfAProduct/{product_id}',  'Users\Buyers@getAllReviewsOfAProduct');

  //SELLERS
  Route::post('/checkIfEmailSent',  'Users\Sellers@checkIfEmailSent');
  Route::post('/verifyEmail',  'Users\Sellers@verifyEmail');
  Route::post('/signUpSeller',  'Users\Sellers@signUpSeller');
  Route::post('/checkIfCodeExist',  'Users\Sellers@checkIfCodeExist');
  Route::post('/verifySeller',  'Users\Sellers@verifySeller');
  Route::post('/activateSeller',  'Users\Sellers@activateSeller');
  Route::post('/deactivateSeller',  'Users\Sellers@deactivateSeller');
  Route::post('/updateSellerIfPaid',  'Users\Sellers@updateSellerIfPaid');
  Route::post('/searchPaidUnpaidSeller',  'Users\Sellers@searchPaidUnpaidSeller');
  
  Route::get('/getUnverifiedSellers',  'Users\Sellers@getUnverifiedSellers');
  Route::get('/getAllSellers',  'Users\Sellers@getAllSellers');
  Route::get('/getDeliveryOrders/{seller_id}',  'Users\Sellers@getDeliveryOrders');
  Route::get('/listsOfSellers',  'Users\Sellers@listsOfSellers');
  
  //PRODUCTS
  Route::post('/addProduct',  'Products\Products@addProduct');
  Route::post('/cartOrderSummary',  'Products\Products@cartOrderSummary');
  Route::post('/removeProductToCart',  'Products\Products@removeProductToCart');
  Route::post('/searchByProductName',  'Products\Products@searchByProductName');
  Route::post('/updateProduct',  'Products\Products@updateProduct');
  Route::post('/deactivateProduct',  'Products\Products@deactivateProduct');
  Route::post('/activateProduct',  'Products\Products@activateProduct');
  Route::post('/searchByCategory',  'Products\Products@searchByCategory');
  Route::post('/searchByPlaces',  'Products\Products@searchByPlaces');

  //RESERVATION
  Route::post('/reserveProductByBuyer',  'Products\Reservations@reserveProductByBuyer');
  Route::post('/deleteReservation',  'Products\Reservations@deleteReservation');
  Route::post('/searchReservationDetails',  'Products\Reservations@searchReservationDetails');
  Route::post('/moveToCart',  'Products\Reservations@moveToCart');

  Route::get('/getReservationDetails/{id}',  'Products\Reservations@getReservationDetails');
    
  Route::get('/getProductsOfSpecificSeller/{email}',  'Products\Products@getProductsOfSpecificSeller');
  Route::get('/getProductDetails/{id}',  'Products\Products@getProductDetails');
  Route::get('/getProductsOfAllSellers',  'Products\Products@getProductsOfAllSellers');
  Route::get('/getOrdersSummary/{email}',  'Products\Products@getOrdersSummary');
  Route::get('/getInventoryReportOfSeller/{seller_id}',  'Products\Products@getInventoryReportOfSeller');
  Route::get('/getDeactivateProducts/{seller_id}',  'Products\Products@getDeactivateProducts');

  //DELIVERY
  Route::post('/createDeliveryCheckout',  'Products\Deliveries@createDeliveryCheckout');

  Route::get('/getAllDeliveries/{seller_id}',  'Products\Deliveries@getAllDeliveries');
  
  // FEEDBACKS
  Route::post('/giveFeedbacks',  'Products\Feedbacks@giveFeedbacks');
  
  Route::get('/getAllFeedbackByProduct/{product_id}',  'Products\Feedbacks@getAllFeedbackByProduct');
});
