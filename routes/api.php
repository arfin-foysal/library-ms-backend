<?php

use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CounteryController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\ItemOrderController;
use App\Http\Controllers\Admin\ItemReceiveController;
use App\Http\Controllers\Admin\ItemRentController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\MembershipPlansController;
use App\Http\Controllers\Admin\PublisherController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\ThirdSubCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Client\ClientController;
use App\Models\User;
use Illuminate\Support\Facades\Log;

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




//auth api
Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::post('/auth/client-login', [AuthController::class, 'clientLogin']);



Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::prefix('admin')->group(function () {
        // Author api
        Route::get('/all-author-list', [AuthorController::class, 'allAuthorList']);
        Route::get('/single-author/{id}', [AuthorController::class, 'singleAuthor']);
        Route::post('/create-or-update-author', [AuthorController::class, 'createOrUpdateAuthor']);
        Route::delete('/delete-author/{id}', [AuthorController::class, 'deleteAuthor']);

        //Catagory api
        Route::get('/all-category-list', [CategoryController::class, 'allCategoryList']);
        Route::get('/single-category/{id}', [CategoryController::class, 'singleCategory']);
        Route::post('/create-or-update-category', [CategoryController::class, 'createOrUpdateCategory']);
        Route::delete('/delete-category/{id}', [CategoryController::class, 'deleteCategory']);

        //SubCatagory api
        Route::get('all-sub-category-list', [SubCategoryController::class, 'allSubCategoryList']);
        Route::get('sub-category-by-category-id/{category_id}', [SubCategoryController::class, 'subCategorybyCategoryId']);
        Route::post('create-or-update-sub-category', [SubCategoryController::class, 'createOrUpdateSubCategory']);
        Route::delete('delete-sub-category/{id}', [SubCategoryController::class, 'deleteSubCategory']);



        //Third Sub Catagory api
        Route::get('all-third-sub-category-list', [ThirdSubCategoryController::class, 'allThirdSubCategoryList']);
        Route::get('third-sub-category-by-sub-category-id/{sub_category_id}', [ThirdSubCategoryController::class, 'ThirdSubCategorybySubCategoryId']);
        Route::post('create-or-update-third-sub-category', [ThirdSubCategoryController::class, 'createOrUpdateThirdSubCategory']);
        Route::delete('delete-third-sub-category/{id}', [ThirdSubCategoryController::class, 'deleteThirdSubCategory']);

        //Language api
        Route::get('all-language-list', [LanguageController::class, 'allLanguage']);
        Route::post('create-or-update-language', [LanguageController::class, 'createOrUpdateLanguage']);
        Route::delete('delete-language/{id}', [LanguageController::class, 'deleteLanguage']);

        //Country api
        Route::get('all-countery-list', [CounteryController::class, 'allCounteryList']);
        Route::post('create-or-update-countery', [CounteryController::class, 'createOrUpdateCountery']);
        Route::delete('delete-countery/{id}', [CounteryController::class, 'deleteCountery']);

        //Publishar api
        Route::get('all-publishar-list', [PublisherController::class, 'allPublisharList']);
        Route::post('create-or-update-publishar', [PublisherController::class, 'createOrUpdatePublishar']);
        Route::delete('delete-publishar/{id}', [PublisherController::class, 'deletePublishar']);


        //Vendor api
        Route::get('all-vendor-list', [VendorController::class, 'allVendorList']);
        Route::get('vendore-payment-list', [VendorController::class, 'vendorPaymentList']);
        Route::post('create-or-update-vendor', [VendorController::class, 'createOrUpdateVendor']);
        Route::delete('delete-vendor/{id}', [VendorController::class, 'deleteVendor']);

        Route::post('vendor-payment-update', [VendorController::class, 'vendorPaymentUpdate']);


        //Membership api
        Route::get('all-membership-list', [MembershipPlansController::class, 'allMembershipList']);
        Route::post('create-or-update-membership', [MembershipPlansController::class, 'createOrUpdateMembership']);
        Route::delete('delete-membership/{id}', [MembershipPlansController::class, 'deleteMembership']);

        //User api
        Route::get('all-user-list', [UserController::class, 'allUserList']);
        Route::post('create-or-update-user', [UserController::class, 'createOrUpdateUser']);
        Route::delete('delete-user/{id}', [UserController::class, 'deleteUser']);
        Route::post('reset-password', [UserController::class, 'passwordReset']);

        //Item api
        Route::get('all-item-list', [ItemController::class, 'allItemList']);
        Route::get('get-item-for-select-field', [ItemController::class, 'getItemForSelectField']);
        Route::post('create-or-update-item', [ItemController::class, 'createOrUpdateItem']);
        Route::delete('delete-item/{id}', [ItemController::class, 'deleteItem']);

        // item order
        Route::post('item-order', [ItemOrderController::class, 'itemOrder']);
        Route::get('all-item-order', [ItemOrderController::class, 'itemList']);
        Route::delete('delete-item-order/{id}', [ItemOrderController::class, 'orderDelete']);

        //Order receved
        Route::get('unreceved-item-by-order-id/{id}', [ItemReceiveController::class, 'unRecevedItemByOrderId']);
        Route::post('item-order-receved', [ItemReceiveController::class, 'itemOrderReceve']);
        Route::get('all-item-receved-list', [ItemReceiveController::class, 'recevedOrderList']);

        //item rents
        Route::get('user-list-for-book-issue', [UserController::class, 'userListforBookIssue']);
        Route::post('item-rent-create', [ItemRentController::class, 'itemRentCreate']);
        Route::get('item-and-available-qty', [ItemRentController::class, 'itemAndAvailableQty']);
        Route::get('item-rent-list', [ItemRentController::class, 'itemRenstList']);
        Route::delete('item-rent-delete/{id}', [ItemRentController::class, 'deleteRentsItem']);
        Route::post('book-rent-active/{id}', [ItemRentController::class, 'bookRentActive']);
        Route::get('date-expired-item', [ItemRentController::class, 'dateExpiredItem']);

        //item return
        Route::post('item-return', [ItemRentController::class, 'returnItem']);
        //item damage
        Route::get('item-damage-list', [ItemRentController::class, 'damagedItemList']);
    });
});
Route::prefix('client')->group(function () {

    Route::get("get-all-item", [ClientController::class, 'getAllBook']);

    Route::get("get-home-page-book", [ClientController::class, 'getHomePageBook']);
    Route::get("get-item-by-id/{id}", [ClientController::class, 'getItemById']);
    Route::get("get-author-and-item", [ClientController::class, 'authorDetailsAndBook']);
    Route::get("single-user", [ClientController::class, 'singleUser'])->middleware(['auth:sanctum']);
    Route::post("profile-update", [ClientController::class, 'profileUpdate'])->middleware(['auth:sanctum']);
    Route::get("rent-item-by-user", [ClientController::class, 'rentItemByUser'])->middleware(['auth:sanctum']);
    Route::get("pending-order-list", [ClientController::class, 'pendingOrderList'])->middleware(['auth:sanctum']);
    Route::post("item-rent-create-client", [ItemRentController::class, 'itemRentCreate'])->middleware(['auth:sanctum']);
    Route::get("item-return-time-expired", [ClientController::class, 'ItemReturnTimeExpired'])->middleware(['auth:sanctum']);
    Route::get("virtual-item-view/{id}", [ClientController::class, 'virtualItemView'])->middleware(['auth:sanctum']);
});


Route::get('test', function () {
    return "test";
});


Route::any('{url}', function () {;
    return response()->json([
        'status' => false,
        'message' => 'Route Not Found!',
        'data' => []
    ], 404);
})->where('url', '.*');
