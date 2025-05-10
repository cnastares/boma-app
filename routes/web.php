<?php

use App\Livewire\Reservation\OrderConfirmation;
use App\Http\Controllers\ChatifyController;
use App\Http\Controllers\Export\DownloadExport;
use App\Http\Controllers\Reservation\PaymentCallbackController;
use App\Livewire\Ad\AdList;
use App\Livewire\User\AdModifications;
use App\Livewire\Home\Home;
use App\Livewire\Ad\PostAd\PostAd;
use App\Livewire\User\MyFavorites;
use App\Livewire\User\MyMessages;
use App\Livewire\User\MyProfile;
use App\Livewire\User\ViewProfile;

use App\Livewire\Ad\SuccessAd;
use App\Livewire\Ad\SuccessUpgrade;
use App\Livewire\Ad\VerificationRequired;
use App\Livewire\AdType\AdOverview;
use App\Livewire\AdType\AdTypeCollection;
use App\Livewire\Notification\Registration;
use App\Livewire\Reservation\CartSummary;
use App\Livewire\Reservation\CheckoutSummary;
use App\Livewire\Reservation\Purchases\MyPurchases;
use App\Livewire\Reservation\Purchases\ViewPurchase;
use App\Livewire\User\MyAccount;
use App\Livewire\User\PageDetail;
use App\Livewire\User\Contact;
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

Route::get('/manifest.json', '\App\Http\Controllers\PwaController@manifest');

Route::get('/', function () {
    return view('welcome');
});


Route::group([], function () {
    Route::get('/', Home::class)->name('home');
    // Route::get('/categories/{category}/{subcategory?}', AdList::class)->name('ad-category');
    // Route::get('/search', AdList::class)->name('search');
    // Route::get('/ad/{slug}', AdDetails::class)->name('ad-details');
    Route::get('/pages/{page:slug}', PageDetail::class)->name('page-details');
    Route::get('/contact', Contact::class)->name('contact');
    Route::get('/profile/{slug}/{id}', ViewProfile::class)->name('view-profile');
    // Route::get('/location/{location}/{category}/{subcategory?}', AdList::class)->name('location-category');
    Route::get('/notification/register', Registration::class);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/post-ad', PostAd::class)->name('post-ad');
    Route::get('/success-ad/{id}', SuccessAd::class)->name('success-ad');
    Route::get('/success/ad-upgrade', SuccessUpgrade::class)->name('success-upgrade');
    Route::get('/my-account', MyAccount::class)->name('my-account');
    Route::get('/my-messages', MyMessages::class)->name('my-messages');
    Route::get('/my-profile', MyProfile::class)->name('my-profile');
    Route::get('/my-favorites', MyFavorites::class)->name('my-favorites');
    Route::get('/verification-required', VerificationRequired::class)->name('verification-required');
    Route::get('/ad-modifications/{id}', AdModifications::class)->name('ad-modifications');
    Route::post('/check-can-message', [ChatifyController::class, 'canReceiveMessage'])->name('check-can-message');
});


// Callback routes for payment gateways
Route::namespace('App\Http\Controllers\Callback')->prefix('callback')->group(function () {
    // Stripe
    Route::get('stripe', 'StripeController@callback');
});

Route::get('/admin/exports/{export}/download', DownloadExport::class)
    ->name('filament.exports.download');


Route::group([], function () {
    Route::get('/ad-type/{type}/{category?}/{subCategory?}/{childCategory?}', AdTypeCollection::class)->name('ad-type.collection');
    Route::get('/location/{location}/{category?}/{subcategory?}/{childCategory?}', AdTypeCollection::class)->name('location-category');
    Route::get('/ad/{slug}', AdOverview::class)->name('ad.overview');
    Route::get('/categories/{category?}/{subCategory?}/{childCategory?}', AdTypeCollection::class)->name('categories.collection');
    Route::get('/search', AdTypeCollection::class)->name('search');

});

Route::group(['prefix' => 'reservation/callback/payment', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/{temporaryOrderId}/stripe', [PaymentCallbackController::class, 'stripe'])->name('reservation.payment-callback.stripe');
    Route::get('/{temporaryOrderId}/paypal', [PaymentCallbackController::class, 'paypal'])->name('reservation.payment-callback.paypal');
    Route::get('/{temporaryOrderId}/flutterwave', [PaymentCallbackController::class, 'flutterwave'])->name('reservation.payment-callback.flutterwave');
    Route::get('/{temporaryOrderId}/paymongo', [PaymentCallbackController::class, 'paymongo'])->name('reservation.payment-callback.paymongo');
    Route::get('/{temporaryOrderId}/{payment_method}/place-order', [PaymentCallbackController::class, 'offline'])->name('reservation.payment-callback.offline');
    Route::get('/{temporaryOrderId}/point-based-order', [PaymentCallbackController::class, 'pointBasedOrder'])->name('reservation.payment-callback.point_based_order');
});

Route::get('/cart-summary/{id?}', CartSummary::class)->name('reservation.cart-summary');
Route::get('/checkout-summary', CheckoutSummary::class)->name('reservation.checkout-summary');
Route::get('/order-confirmation', OrderConfirmation::class)->name('reservation.order-confirmation');

Route::get('/my-purchases', MyPurchases::class)->name('reservation.my-purchases');
Route::get('/view-purchase/{id}', ViewPurchase::class)->name('reservation.view-purchases');

require __DIR__ . '/auth.php';
