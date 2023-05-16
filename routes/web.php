<?php
use App\Http\Controllers\ApplicationController;

use App\Http\Controllers\Admin\AccessoryController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PartController;
use App\Http\Controllers\Admin\RepairController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/admin/dashboard', function () {
//     return view('dashboard');
// });

// Users Routes
Route::group(['prefix' => '/api/users'], function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{user}', [UserController::class, 'update']);
    Route::delete('/{user}', [UserController::class, 'destroy']);
    Route::delete('/', [UserController::class, 'bulkDelete']);
});



// Accessories Routes
Route::group(['prefix' => '/api/accessories'], function () {
    Route::get('/', [AccessoryController::class, 'index']);
    Route::post('/', [AccessoryController::class, 'store']);
    Route::put('/{accessory}', [AccessoryController::class, 'update']);
    Route::delete('/{accessory}', [AccessoryController::class, 'destroy']);
    Route::delete('/', [AccessoryController::class, 'bulkDelete']);
});

// Clients Routes
Route::group(['prefix' => '/api/clients'], function () {
    Route::get('/', [ClientController::class, 'index']);
    Route::post('/', [ClientController::class, 'store']);
    Route::put('/{client}', [ClientController::class, 'update']);
    Route::delete('/{client}', [ClientController::class, 'destroy']);
});

// Devices Routes
Route::group(['prefix' => '/api/devices'], function () {
    Route::get('/', [DeviceController::class, 'index']);
    Route::post('/', [DeviceController::class, 'store']);
    Route::put('/{device}', [DeviceController::class, 'update']);
    Route::delete('/{device}', [DeviceController::class, 'destroy']);
});

// Locations Routes
Route::group(['prefix' => '/api/locations'], function () {
    Route::get('/', [LocationController::class, 'index']);
    Route::post('/', [LocationController::class, 'store']);
    Route::put('/{location}', [LocationController::class, 'update']);
    Route::delete('/{location}', [LocationController::class, 'destroy']);
});

// Parts Routes
Route::group(['prefix' => '/api/parts'], function () {
    Route::get('/', [PartController::class, 'index']);
    Route::post('/', [PartController::class, 'store']);
    Route::put('/{part}', [PartController::class, 'update']);
    Route::delete('/{part}', [PartController::class, 'destroy']);
});

// Repairs Routes
Route::group(['prefix' => '/api/repairs'], function () {
    Route::get('/', [RepairController::class, 'index']);
    Route::post('/', [RepairController::class, 'store']);
    Route::put('/{repair}', [RepairController::class, 'update']);
    Route::delete('/{repair}', [RepairController::class, 'destroy']);
});

// Suppliers Routes
Route::group(['prefix' => '/api/suppliers'], function () {
    Route::get('/', [SupplierController::class, 'index']);
    Route::post('/', [SupplierController::class, 'store']);
    Route::put('/{supplier}', [SupplierController::class, 'update']);
    Route::delete('/{supplier}', [SupplierController::class, 'destroy']);
});


Route::get('{view}', ApplicationController::class)->where('view', '(.*)');
