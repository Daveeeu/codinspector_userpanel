<?php

use App\Http\Controllers\NotificationController;
use App\Mail\ReferralSummary;
use App\Mail\SubscriptionReminder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
    use App\Http\Controllers\Auth\LoginController;
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
Auth::routes(['verify' => true]);

Route::get('2fa/verify', [LoginController::class, 'show2faForm'])->name('2fa.verify');
Route::post('2fa/verify', [LoginController::class, 'verifyTwoFactor']);

    // Fiók beállítások oldal
    Route::get('/account/settings', [\App\Http\Controllers\AccountController::class, 'showSettings'])->name('account.settings');

    // Fiókadatok frissítése
    Route::put('/account/settings', [\App\Http\Controllers\AccountController::class, 'updateSettings'])->name('account.update');
    Route::delete('/account/delete', [\App\Http\Controllers\AccountController::class, 'destroy'])->name('account.delete');

    Route::middleware('auth')->group(function () {
        Route::get('store/create', [\App\Http\Controllers\StoreController::class, 'create'])->name('store.create');
        Route::post('store', [\App\Http\Controllers\StoreController::class, 'store'])->name('store.store');
        Route::get('store', [\App\Http\Controllers\StoreController::class, 'index'])->name('store.index');
        Route::get('store/{store}', [\App\Http\Controllers\StoreController::class, 'show'])->name('store.details')->middleware('can:view,store');
        Route::get('/store/{store}/select-package', [\App\Http\Controllers\StoreController::class, 'selectPackage'])->name('store.update.package')->middleware('can:update,store');

        Route::put('/store/{store}', [\App\Http\Controllers\StoreController::class, 'update'])->name('store.update')->middleware('can:update,store');


        Route::prefix('api')->group(function () {
            Route::post('/subscriptions/create', [\App\Http\Controllers\SubscriptionController::class, 'create']);
            Route::post('/subscriptions/{id}/cancel', [\App\Http\Controllers\SubscriptionController::class, 'cancel']);
        });

        //Route::resource('exceptions', \App\Http\Controllers\ExceptionController::class);
        Route::controller(\App\Http\Controllers\ExceptionController::class)->prefix('exceptions')->name('exceptions.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('filter','filter')->name('filter');

            Route::post('/','store')->name('store');

            Route::patch('/{exception}', 'update')->name('update')->middleware('can:update,exception');
            Route::delete('/{exception}', 'destroy')->name('destroy')->middleware('can:delete,exception');
        });

        Route::resource('manual-query', \App\Http\Controllers\ManualQueryController::class);
        Route::post('manual-query/check-emails', [\App\Http\Controllers\ManualQueryController::class, 'checkEmails'])->name('manual-query.check-email');
        Route::post('/statistics/update-charts', [\App\Http\Controllers\HomeController::class, 'updateCharts'])->name('statistics.updateCharts');

        Route::get('notifications', [\App\Http\Controllers\NotificationsController::class, 'index'])->name('notifications.index');
        Route::put('notifications', [\App\Http\Controllers\NotificationsController::class, 'update'])->name('notifications.update');

        Route::controller(\App\Http\Controllers\FeedbackController::class)->prefix('feedback')->group(function () {
            Route::get('', 'index')->name('feedback.index');
            Route::get('/filter', 'filter')->name('feedback.filter');
        });

        Route::controller(\App\Http\Controllers\QueryController::class)->prefix('queries')->group(function () {
            Route::get('', 'index')->name('queries.index');
            Route::get('/queries', 'filter')->name('queries.filter');
        });
        Route::get('/referral', [\App\Http\Controllers\ReferralController::class, 'show'])->name('referral.show');
        Route::post('/referral', [\App\Http\Controllers\ReferralController::class, 'generate'])->name('referral.generate');
        Route::get('/partner-program', [\App\Http\Controllers\PartnerProgramController::class, 'index'])->name('partner_program.index');
        Route::post('/partner-program', [\App\Http\Controllers\PartnerProgramController::class, 'store'])->name('partner_program.store');

        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\UserNotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
        Route::post('/notifications/mark-as-deleted', [\App\Http\Controllers\UserNotificationController::class, 'markAsDeleted'])->name('notifications.markAsDeleted');
        Route::post('/notifications/delete-all', [\App\Http\Controllers\UserNotificationController::class, 'deleteAll'])->name('notifications.deleteAll');
        Route::post('/notifications/get-modal-content', [\App\Http\Controllers\UserNotificationController::class, 'getModalContent'])->name('notifications.getModalContent');

    });
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    // Define a group of routes with 'auth' middleware applied
Route::middleware(['auth', 'verified', '2fa'])->group(function () {
    // Define a GET route for the root URL ('/')
    Route::get('/', function () {
        // Return a view named 'index' when accessing the root URL
        return redirect()->route('home');
    });

});


    Route::post('/stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook']);

