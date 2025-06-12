<?php

use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

/* Controllers */
use App\Http\Controllers\PwaController;
use App\Http\Controllers\ChatifyController;
use App\Http\Controllers\Export\DownloadExport;
use App\Http\Controllers\Reservation\PaymentCallbackController;
use App\Http\Controllers\Livewire\CustomFileUploadController;
use App\Http\Controllers\ProfileController;

/* Livewire Components */
use App\Livewire\Home\Home;
use App\Livewire\User\Contact;
use App\Livewire\User\ViewProfile;
use App\Livewire\User\PageDetail;
use App\Livewire\User\MyProfile;
use App\Livewire\User\MyAccount;
use App\Livewire\User\MyMessages;
use App\Livewire\User\MyFavorites;
use App\Livewire\User\AdModifications;

use App\Livewire\Ad\PostAd\PostAd;
use App\Livewire\Ad\SuccessAd;
use App\Livewire\Ad\SuccessUpgrade;
use App\Livewire\Ad\VerificationRequired;
use App\Livewire\AdType\AdTypeCollection;
use App\Livewire\AdType\AdOverview;

use App\Livewire\Notification\Registration;

use App\Livewire\Reservation\CartSummary;
use App\Livewire\Reservation\CheckoutSummary;
use App\Livewire\Reservation\OrderConfirmation;
use App\Livewire\Reservation\Purchases\MyPurchases;
use App\Livewire\Reservation\Purchases\ViewPurchase;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

/* Endpoints internos de Livewire */
// Livewire::routes();  // <--- ELIMINA o comenta esta línea

/* Manifest PWA */
Route::get('/manifest.json', [PwaController::class, 'manifest'])->name('pwa.manifest');

/* Sobrescribir el endpoint de subida por defecto de Livewire */
Route::post('/livewire/upload-file', [CustomFileUploadController::class, 'handle'])
    ->name('livewire.upload-file');

/* --- Páginas públicas --- */
Route::get('/', Home::class)->name('home');
Route::get('/pages/{page:slug}', PageDetail::class)->name('page.details');
Route::get('/contact', Contact::class)->name('contact');
Route::get('/profile/{slug}/{id}', ViewProfile::class)->name('view.profile');
Route::get('/notification/register', Registration::class);

/* Navegación y catálogos */
Route::get('/ad-type/{type}/{category?}/{subCategory?}/{childCategory?}', AdTypeCollection::class)
    ->name('adtype.collection');
Route::get('/location/{location}/{category?}/{subcategory?}/{childCategory?}', AdTypeCollection::class)
    ->name('location.collection');
Route::get('/ad/{slug}', AdOverview::class)->name('ad.overview');
Route::get('/categories/{category?}/{subCategory?}/{childCategory?}', AdTypeCollection::class)
    ->name('categories.collection');
Route::get('/search', AdTypeCollection::class)->name('search');

/* --- Rutas protegidas --- */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/post-ad', PostAd::class)->name('postad');
    Route::get('/success-ad/{id}', SuccessAd::class)->name('success.ad');
    Route::get('/success/ad-upgrade', SuccessUpgrade::class)->name('success.upgrade');
    Route::get('/my-account', MyAccount::class)->name('my.account');
    Route::get('/my-messages', MyMessages::class)->name('my.messages');
    Route::get('/my-profile', MyProfile::class)->name('my.profile');
    Route::get('/my-favorites', MyFavorites::class)->name('my.favorites');
    Route::get('/verification-required', VerificationRequired::class)->name('verification.required');
    Route::get('/ad-modifications/{id}', AdModifications::class)->name('ad.modifications');

    /* AJAX – ¿puede enviar mensaje? */
    Route::post('/check-can-message', [ChatifyController::class, 'canReceiveMessage'])
        ->name('check.can.message');
});

/* --- Callbacks de pasarelas de pago --- */
Route::prefix('reservation/callback/payment')
      ->name('reservation.payment-callback.')
      ->group(function () {
        Route::get('/{temporaryOrderId}/stripe',      [PaymentCallbackController::class, 'stripe'])->name('stripe');
        Route::get('/{temporaryOrderId}/paypal',      [PaymentCallbackController::class, 'paypal'])->name('paypal');
        Route::get('/{temporaryOrderId}/flutterwave', [PaymentCallbackController::class, 'flutterwave'])->name('flutterwave');
        Route::get('/{temporaryOrderId}/paymongo',    [PaymentCallbackController::class, 'paymongo'])->name('paymongo');
        Route::get('/{temporaryOrderId}/{method}/place-order', [PaymentCallbackController::class, 'offline'])->name('offline');
        Route::get('/{temporaryOrderId}/point-based-order',    [PaymentCallbackController::class, 'pointBasedOrder'])->name('pointbased');
});

/* Carrito y compras */
Route::get('/cart-summary/{id?}',      CartSummary::class)->name('reservation.cartsummary');
Route::get('/checkout-summary',        CheckoutSummary::class)->name('reservation.checkoutsummary');
Route::get('/order-confirmation',      OrderConfirmation::class)->name('reservation.orderconfirmation');
Route::get('/my-purchases',            MyPurchases::class)->name('reservation.mypurchases');
Route::get('/view-purchase/{id}',      ViewPurchase::class)->name('reservation.viewpurchase');

/* Exports (Filament) */
Route::get('/admin/exports/{export}/download', DownloadExport::class)
      ->middleware(['auth', 'verified', 'can:download-exports'])
      ->name('filament.exports.download');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/* Auth scaffolding */
require __DIR__.'/auth.php';
