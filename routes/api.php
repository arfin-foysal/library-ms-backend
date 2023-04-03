<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;




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

// Route::get('storage-link', function () {
//     $targetFolder = public_path('app/public');
//     $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
//     symlink($linkFolder, $targetFolder);
//     return 'The [public/storage] folder has been linked';
// });




Route::group(['middleware' => ['auth:sanctum']], function () {
    // user api
    
});



//Authentaction
Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
