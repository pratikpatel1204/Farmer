<?php

use App\Http\Controllers\Api\V1\AppController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\EnquiryController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\Member\AccountController;
use App\Http\Controllers\Api\V1\Member\DeviceTokenController;
use App\Http\Controllers\Api\V1\Member\InquiryController;
use App\Http\Controllers\Api\V1\Member\KycController;
use App\Http\Controllers\Api\V1\Member\NotificationController;
use App\Http\Controllers\Api\V1\Member\ProductController as MemberProductController;
use App\Http\Controllers\Api\V1\Member\ProfileController as MemberProfileController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\SubCategoryController;
use App\Http\Controllers\Api\V1\SurveyController;
use App\Http\Controllers\Api\PolicyController;
use App\Http\Middleware\ApiActiveMember;
use App\Http\Middleware\ApiForceJson;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->middleware([ApiForceJson::class])->group(function () {

    /* ---------------- Public (login ki zarurat nahi) ---------------- */
    Route::middleware('throttle:60,1,km-public')->group(function () {

        Route::get('policies', [PolicyController::class, 'index']);
        Route::get('policies/{type}', [PolicyController::class, 'show']);
        Route::get('app/config', [AppController::class, 'config'])->name('config');
        Route::get('pages/about', [AppController::class, 'about'])->name('about');
        Route::get('contact', [AppController::class, 'contact'])->name('contact');
        Route::get('states', [AppController::class, 'states'])->name('states');
        Route::get('states/{stateId}/cities', [AppController::class, 'cities'])->name('cities');

        Route::get('home', [HomeController::class, 'index'])->name('home');

        Route::get('categories', [CategoryController::class, 'index'])->name('categories');
        Route::get('categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');
        Route::get('sub-categories', [SubCategoryController::class, 'index'])->name('sub-categories');

        Route::get('products', [ProductController::class, 'index'])->name('products');
        Route::get('products/{slug}', [ProductController::class, 'show'])->name('products.show');

        Route::get('profiles', [ProfileController::class, 'index'])->name('profiles');
        Route::get('profiles/{uniqueId}', [ProfileController::class, 'show'])->name('profiles.show');

        Route::get('blogs', [BlogController::class, 'index'])->name('blogs');
        Route::get('blogs/{slug}', [BlogController::class, 'show'])->name('blogs.show');

        Route::get('survey', [SurveyController::class, 'show'])->name('survey');
    });

    Route::post('contact', [AppController::class, 'sendContact'])->middleware('throttle:5,1,km-contact')->name('contact.send');

    /* ---------------- Auth ---------------- */
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::middleware('throttle:30,1,km-check')->group(function () {
            Route::get('check-sponsor/{id}', [AuthController::class, 'checkSponsor'])->name('check.sponsor');
            Route::get('check-mobile', [AuthController::class, 'checkMobile'])->name('check.mobile');
            Route::get('check-email', [AuthController::class, 'checkEmail'])->name('check.email');
        });

        Route::post('register', [AuthController::class, 'register'])->middleware('throttle:10,1,km-register')->name('register');
        Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1,km-login')->name('login');

        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:5,1,km-forgot')->name('forgot');
        Route::post('forgot-password/resend', [AuthController::class, 'resendOtp'])->middleware('throttle:5,1,km-resend')->name('forgot.resend');
        Route::post('forgot-password/verify', [AuthController::class, 'verifyOtp'])->middleware('throttle:10,1,km-verify')->name('forgot.verify');
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:10,1,km-reset')->name('reset');

        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
    });

    /* ---------------- Login zaroori (Farmer / Non Farmer) ---------------- */
    Route::middleware(['auth:sanctum', ApiActiveMember::class])->group(function () {

        // Website jaisa: 1 minute me 5 try, aur ek product par 24 ghante me ek enquiry (controller me)
        Route::post('products/{slug}/enquiry', [EnquiryController::class, 'store'])->middleware('throttle:5,1,km-enquiry')->name('products.enquiry');

        Route::post('survey', [SurveyController::class, 'submit'])->middleware('throttle:20,1,km-survey')->name('survey.submit');

        Route::prefix('me')->name('me.')->middleware('throttle:120,1,km-member')->group(function () {
            Route::get('/', [AccountController::class, 'me'])->name('show');
            Route::get('dashboard', [AccountController::class, 'dashboard'])->name('dashboard');
            Route::get('team', [AccountController::class, 'team'])->name('team');

            // My products
            Route::get('products', [MemberProductController::class, 'index'])->name('products');
            Route::post('products', [MemberProductController::class, 'store'])->name('products.store');
            Route::get('products/{id}', [MemberProductController::class, 'show'])->whereNumber('id')->name('products.show');
            Route::post('products/{id}', [MemberProductController::class, 'update'])->whereNumber('id')->name('products.update');
            Route::delete('products/{id}', [MemberProductController::class, 'destroy'])->whereNumber('id')->name('products.destroy');
            Route::post('products/{id}/complete', [MemberProductController::class, 'complete'])->whereNumber('id')->name('products.complete');

            // Enquiries
            Route::get('inquiries', [InquiryController::class, 'index'])->name('inquiries');
            Route::get('inquiries/sent', [InquiryController::class, 'sent'])->name('inquiries.sent');
            Route::get('inquiries/products/{productId}', [InquiryController::class, 'byProduct'])->whereNumber('productId')->name('inquiries.product');
            Route::patch('inquiries/{id}/status', [InquiryController::class, 'status'])->whereNumber('id')->name('inquiries.status');

            // Notifications
            Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
            Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.count');
            Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
            Route::delete('notifications/read', [NotificationController::class, 'clearRead'])->name('notifications.clearRead');
            Route::post('notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
            Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

            // Business profile
            Route::get('profile', [MemberProfileController::class, 'show'])->name('profile');
            Route::post('profile/personal', [MemberProfileController::class, 'personal'])->name('profile.personal');
            Route::post('profile/about', [MemberProfileController::class, 'about'])->name('profile.about');
            Route::post('profile/images', [MemberProfileController::class, 'images'])->name('profile.images');
            Route::post('profile/video', [MemberProfileController::class, 'video'])->name('profile.video');
            Route::delete('profile/media', [MemberProfileController::class, 'deleteMedia'])->name('profile.media.delete');

            // KYC
            Route::get('kyc', [KycController::class, 'show'])->name('kyc');
            Route::post('kyc', [KycController::class, 'update'])->name('kyc.update');

            // Push (FCM) device token
            Route::post('device-token', [DeviceTokenController::class, 'store'])->name('device.store');
            Route::delete('device-token', [DeviceTokenController::class, 'destroy'])->name('device.destroy');
        });
    });
});
