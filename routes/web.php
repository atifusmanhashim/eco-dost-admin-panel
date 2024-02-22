<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AddUserController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\VehicleController;
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
// Route::get('/', function () {
//     return view('welcome');
// });

//Loading Dashboard - Object: A001
Route::get('/dashboard',[HomeController::class,'dashboard'])->name('dashboard');


//Loading Dashboard or Login Page - Object: A002
Route::get('/',[HomeController::class,'index'])->name('root');

//All Users
Route::get('/users',[HomeController::class,'users_lst'])->name('users');
Route::get('/adduser',[AddUserController::class,'index'])->name('adduser');
Route::get('/edituser/{id}',[AddUserController::class,'edituser'])->name('edituser');

//Login Page - Object: A003
Route::get('/login',[LoginController::class,'index'])->name('login');

//Logging into the System - Object: A004
Route::post('postlogin', [LoginController::class, 'postLogin'])->name('postlogin');

//Logout - Object: A005
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

//User Registration, OTP, Edit User - Object: A005
Route::get('/user-register',[RegisterController::class,'index'])->name('user-register');

Route::get('/otpscreen',[RegisterController::class,'otpscreen'])->name('otpscreen');

//Complaints
Route::get('/complaints',[ComplaintController::class,'index'])->name('complaints');
Route::get('/pending_approval_complaints',[ComplaintController::class,'pending_approval'])->name('pending_approval');

//Inventory
Route::get('/inventory',[InventoryController::class,'index'])->name('inventory');
Route::get('download_template',[InventoryController::class,'download_template'])->name('download_template');

//Vendors
Route::get('/vendors',[VendorController::class,'index'])->name('vendors');
Route::get('/addvendor',[VendorController::class,'addvendor'])->name('addvendor');
Route::get('/editvendor/{id}',[VendorController::class,'editvendor'])->name('editvendor');



//FAQs
Route::get('/faqs',[FAQController::class,'index'])->name('faqs');
Route::get('/addfaq',[FAQController::class,'addfaq'])->name('addfaq');
Route::get('/editfaq/{id}',[FAQController::class,'editfaq'])->name('editfaq');

//Vehicles
Route::get('/vehicles',[VehicleController::class,'index'])->name('vehicles');
Route::get('download_vehicles_list',[VehicleController::class,'download_template'])->name('download_vehicles_list_template');
