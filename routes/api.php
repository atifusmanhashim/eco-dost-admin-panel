<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

#Register Controller
use App\Http\Controllers\RegisterController;

use App\Http\Controllers\LoginController;

use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\PriorityController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\VehicleController;
// use App\Http\Controllers\AddUserController;
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

/**
 *      Admin User APIs
 *
 *
 */
Route::post('verify_email', [RegisterController::class, 'verify_email'])->name('verify_email');

Route::post('register', [RegisterController::class, 'register'])->name('register');

Route::post('delete-user', [RegisterController::class, 'delete_user'])->name('delete-user');

Route::post('login-api', [LoginController::class, 'login'])->name('login-api');


/**
 *
 *
 *      Complaints
 *      1. Add Complaint
 *      2. Update Complaint
 *      3. Delete Complaint
 *      4. Receive Complaint
 *      5. Process Complaint
 *      6. Return Complaint
 *      7. Complaint List
 *      8. In line Complaints
 *      9. Complaint Details
 *
 *
 *
 * **/


Route::post('add-complaint',[ComplaintController::class,'add_complaint']);

Route::post('complaint_details',[ComplaintController::class,'complaint_details'])->name('complaint_details');

Route::post('update_complaint_sc',[ComplaintController::class,'update_complaint_sc'])->name('update_complaint_sc');

Route::post('approval_update_complaint_sc',[ComplaintController::class,'approval_update_complaint_sc'])->name('approval_update_complaint_sc');

Route::post('add_priority',[PriorityController::class,'add_priority']);

Route::get('priority_list',[PriorityController::class,'priority_list']);

Route::get('complaint_lst',[ComplaintController::class,'complaint_lst']);

Route::get('vendor_complaint_lst',[ComplaintController::class,'vendor_complaint_lst']);

Route::post('withdraw_complaint',[ComplaintController::class,'withdraw_complaint']);
Route::get('pendingapprovals',[ComplaintController::class,'pendingapprovals'])->name('pendingapprovals');

/**
 *
 *
 *      Vendor / Dealer API
 *
 */

//Create Vendor
Route::post('create_vendor',[VendorController::class,'create_vendor'])->middleware('auth:api');

//Update Vendor for App
Route::post('update_vendor',[VendorController::class,'update_vendor']);

//Update Vendor for Admin
Route::post('update_vendor_admin',[VendorController::class,'update_vendor_admin'])->middleware('auth:api');

//Making Vendor In-active
Route::post('delete_vendor',[VendorController::class,'delete_vendor'])->middleware('auth:api');

//Login for App
Route::post('login_vendor',[VendorController::class,'login_vendor'])->name('login_vendor');

//With Respect to City
Route::get('dealers_list',[VendorController::class,'dealers_list'])->name('dealers_list');


//With Respect to Type
Route::get('dealers_list_type',[VendorController::class,'dealers_list_type'])->name('dealers_list_type');

/**
 *
 *    Inventory APIs
 *      1. Download Inventory / Download Template
 *      2. Upload Inventory
 *
 */

Route::post('import_inventory',[InventoryController::class,'import_inventory'])->name('import_inventory');

Route::post('inventory_details',[InventoryController::class,'inventory_details'])->name('inventory_details');

//Default
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('update_user_details',[RegisterController::class,'update_user_details'])->middleware('auth:api');


//FAQs
Route::get('/get_faqs',[FAQController::class,'get_faqs'])->name('get_faqs');
Route::get('/get_faq_by_id/{id}',[FAQController::class,'get_faq_by_id'])->name('get_faq_by_id');
Route::post('faq_insert',[FAQController::class,'faq_insert'])->name('faq_insert');
Route::post('delete_faq',[FAQController::class,'delete_faq'])->name('delete_faq');
Route::post('update_faq',[FAQController::class,'update_faq'])->name('update_faq');

//Vehicle Part Warranty Checker
Route::post('warranty_checker',[VehicleController::class,'warranty_checker'])->name('warranty_checker');
Route::post('vehicle_warranty_register',[VehicleController::class,'vehicle_warranty_register'])->name('vehicle_warranty_register');