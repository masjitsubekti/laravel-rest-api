<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JenisProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\SliderController;
use Carbon\Carbon;

date_default_timezone_set('Asia/Jakarta');

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

Route::get('/', function(){
    return json_encode(
        array(
            'project' => 'Shipt Company Profile',
            'time' => Carbon::now()
        )
    );
});

// {{url}}/api/v1
// No Authorization
Route::post('v1/login', [AuthController::class, 'login']);
Route::get('v1/get-jenis-project', [JenisProjectController::class, 'index']);
Route::get('v1/get-client', [ClientController::class, 'index']);
Route::get('v1/get-layanan', [LayananController::class, 'index']);
Route::get('v1/get-project', [ProjectController::class, 'index']);
Route::get('v1/get-project/all', [ProjectController::class, 'getAll']);
Route::get('v1/get-project/{id?}', [ProjectController::class, 'show']);
Route::get('v1/get-artikel/all', [ArtikelController::class, 'getAll'])->name('artikel.all');
Route::get('v1/get-artikel', [ArtikelController::class, 'getArtikelPublish'])->name('artikel.publish');
Route::get('v1/get-artikel/{id?}', [ArtikelController::class, 'show']);
Route::get('v1/get-slider', [SliderController::class, 'getSliderActive'])->name('slider.active');
Route::get('v1/project/image', [ProjectController::class, 'getImageProject']);

// With Authorization
Route::middleware(['jwt.verify'])->prefix('v1')->name('v1.')->group(function () {
    // Auth
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('get-user', [AuthController::class, 'getUser']);

    // Jenis Project
    Route::get('jenis-project', [JenisProjectController::class, 'index'])->name('jenisProject');
    Route::post('jenis-project', [JenisProjectController::class, 'store'])->name('jenisProject');
    Route::get('jenis-project/{id?}', [JenisProjectController::class, 'show']);
    Route::put('jenis-project/{id?}', [JenisProjectController::class, 'update']);
    Route::delete('jenis-project/{id?}', [JenisProjectController::class, 'destroy']);
    
    // Client
    Route::get('client', [ClientController::class, 'index'])->name('client');
    Route::post('client', [ClientController::class, 'store'])->name('client');
    Route::get('client/{id?}', [ClientController::class, 'show']);
    Route::put('client/{id?}', [ClientController::class, 'update']);
    Route::delete('client/{id?}', [ClientController::class, 'destroy']);

    // Layanan / Service
    Route::get('layanan', [LayananController::class, 'index'])->name('layanan');
    Route::post('layanan', [LayananController::class, 'store'])->name('layanan');
    Route::get('layanan/{id?}', [LayananController::class, 'show']);
    Route::put('layanan/{id?}', [LayananController::class, 'update']);
    Route::delete('layanan/{id?}', [LayananController::class, 'destroy']);    

    // Project
    Route::get('project', [ProjectController::class, 'getAll'])->name('project');
    Route::post('project', [ProjectController::class, 'store'])->name('project');
    Route::get('project/{id?}', [ProjectController::class, 'show']);
    Route::put('project/{id?}', [ProjectController::class, 'update']);
    Route::delete('project/{id?}', [ProjectController::class, 'destroy']);
    
    // Project Image
    Route::post('project/upload', [ProjectController::class, 'uploadImage'])->name('project.upload');
    Route::delete('project/image/{id?}', [ProjectController::class, 'deleteImage']);
    
    // Users
    Route::get('user', [UserController::class, 'index'])->name('user');
    Route::post('user', [UserController::class, 'store'])->name('user');
    Route::get('user/{id?}', [UserController::class, 'show']);
    Route::put('user/{id?}', [UserController::class, 'update']);
    Route::delete('user/{id?}', [UserController::class, 'destroy']);

    // Artikel
    Route::get('artikel', [ArtikelController::class, 'index'])->name('artikel');
    Route::post('artikel', [ArtikelController::class, 'store'])->name('artikel');
    Route::get('artikel/{id?}', [ArtikelController::class, 'show']);
    Route::post('artikel-update', [ArtikelController::class, 'update']);
    Route::delete('artikel/{id?}', [ArtikelController::class, 'destroy']);

    // Slider
    Route::get('slider', [SliderController::class, 'index'])->name('slider');
    Route::post('slider', [SliderController::class, 'store'])->name('slider');
    Route::get('slider/{id?}', [SliderController::class, 'show']);
    Route::put('slider/{id?}', [SliderController::class, 'update']);
    Route::delete('slider/{id?}', [SliderController::class, 'destroy']);
    
    // App Config

});

