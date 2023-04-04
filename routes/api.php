<?php

use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\CategoryController;
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

    // Author api
    Route::get('/all-author-list', [AuthorController::class, 'allAuthorList']);
    Route::get('/single-author/{id}', [AuthorController::class, 'singleAuthor']);
    Route::post('/create-or-update-author', [AuthorController::class, 'createOrUpdateAuthor']);
    Route::delete('/delete-author/{id}', [AuthorController::class, 'deleteAuthor']);

    //Catagory api
    Route::get('/all-category-list', [CategoryController::class, 'allCategoryList']);
    Route::get('/single-category/{id}',[CategoryController::class, 'singleCategory']);
    Route::post('/create-or-update-category', [CategoryController::class, 'createOrUpdateCategory']);
    Route::delete('/delete-category/{id}', [CategoryController::class, 'deleteCategory']);
});









//Authentaction
Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
