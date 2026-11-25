<?php

use App\Http\Controllers\Admin\AuthAdminController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ContactSettingController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\LevelController;
use App\Http\Controllers\Admin\MemberDetailsController;
use App\Http\Controllers\Admin\MemberTreeController;
use App\Http\Controllers\Admin\NotificationMasterController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PolicyController;
use App\Http\Controllers\Admin\PopupController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\SurveyAdminController;
use App\Http\Controllers\Admin\SurveyQuestionAdminController;
use App\Http\Controllers\Front\BlogController;
use App\Http\Controllers\Front\CategoryController as FrontCategoryController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Front\ProductInquiryController;
use App\Http\Controllers\Front\ProfileController;
use App\Http\Controllers\Front\SurveyController;
use App\Http\Controllers\Member\AuthMemberController;
use App\Http\Controllers\Member\DashboardMemberController;
use App\Http\Controllers\Member\MLMController;
use App\Http\Controllers\Member\NotificationMemberController;
use App\Http\Controllers\Member\ProductMemberController;
use App\Http\Controllers\Member\ProfileMemberController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Front\PolicyPageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return "Cache cleared!";
});

Route::get('privacy-policy', [PolicyPageController::class, 'privacy'])->name('privacy.policy');
Route::get('terms-and-conditions', [PolicyPageController::class, 'terms'])->name('terms.conditions');


Route::prefix('admin')->group(function () {
    Route::get('/login', [AuthAdminController::class, 'login_form'])->name('admin.login');
    Route::post('/login-submit', [AuthAdminController::class, 'login_submit'])->name('admin.login.submit');

    Route::middleware(['auth:admin'])->name('admin.')->group(function () {

        Route::get('/logout', [AuthAdminController::class, 'logout'])->name('logout');

        Route::get('/notifications', [NotificationController::class, 'adminIndex'])->name('notifications');
        Route::get('/notifications/latest', [NotificationController::class, 'latest'])->name('notifications.latest');
        Route::get('/notifications/{id}/open', [NotificationController::class, 'redirect'])->name('notifications.open');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
        Route::delete('/notifications/clear-read', [NotificationController::class, 'clearRead'])->name('notifications.clearRead');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        
        Route::middleware(['permission:Admin Profile'])->group(function () {
            Route::get('/profile', [AuthAdminController::class, 'admin_profile'])->name('profile');
            Route::post('/profile-update', [AuthAdminController::class, 'admin_profile_update'])->name('profile.update');
        });

        Route::middleware(['permission:Contact Us'])->group(function () {
            Route::get('/contact-us', [ContactSettingController::class, 'index'])->name('contact.index');
            Route::post('/contact-us/update', [ContactSettingController::class, 'update'])->name('contact.update');
        });

        Route::middleware(['permission:Admin Dashboard'])->group(function () {
            Route::get('/dashboard', [DashboardAdminController::class, 'admin_dashboard'])->name('dashboard');
        });

        Route::middleware(['permission:View Roles'])->group(function () {
            Route::get('/roles', [RoleController::class, 'role_list'])->name('roles.list');
        });
        Route::middleware(['permission:Edit Roles'])->group(function () {
            Route::get('/roles-edit/{id}', [RoleController::class, 'role_edit'])->name('roles.edit');
            Route::post('/roles-update', [RoleController::class, 'role_update'])->name('roles.update');
        });

        Route::middleware(['permission:View Permissions'])->group(function () {
            Route::get('/permissions', [PermissionController::class, 'permissions_list'])->name('permissions.list');
        });
        Route::middleware(['permission:Create Permissions'])->group(function () {
            Route::get('/create-permissions', [PermissionController::class, 'permissions_create'])->name('permissions.create');
            Route::post('/permissions-store', [PermissionController::class, 'permissions_store'])->name('permissions.store');
        });
        Route::middleware(['permission:Edit Permissions'])->group(function () {
            Route::get('/permissions-edit/{id}', [PermissionController::class, 'permissions_edit'])->name('permissions.edit');
            Route::post('/permissions-update', [PermissionController::class, 'permissions_update'])->name('permissions.update');
        });

        Route::prefix('state')->middleware(['permission:State Master'])->group(function () {
            Route::get('/', [StateController::class, 'state_list'])->name('state.list');
            Route::get('/table', [StateController::class, 'table'])->name('state.table');
            Route::post('/store', [StateController::class, 'state_store'])->name('state.store');
            Route::post('/update', [StateController::class, 'state_update'])->name('state.update');
            Route::delete('/state-delete/{id}', [StateController::class, 'state_destroy'])->name('state.destroy');
        });

        Route::prefix('city')->middleware(['permission:City Master'])->group(function () {
            Route::get('/', [CityController::class, 'city_list'])->name('city.list');
            Route::get('/table', [CityController::class, 'table'])->name('city.table');
            Route::get('/states', [CityController::class, 'getStates'])->name('city.states');
            Route::post('/store', [CityController::class, 'city_store'])->name('city.store');
            Route::post('/update', [CityController::class, 'city_update'])->name('city.update');
            Route::delete('/city-delete/{id}', [CityController::class, 'city_destroy'])->name('city.destroy');
        });

        Route::middleware(['permission:Privacy Policy'])->group(function () {
            Route::get('/privacy-policy', [PolicyController::class, 'privacy'])->name('privacy.policy');
            Route::post('/update-privacy', [PolicyController::class, 'update_privacy'])->name('update.privacy');
        });

        Route::middleware(['permission:Terms Conditions'])->group(function () {
            Route::get('/terms-conditions', [PolicyController::class, 'term'])->name('terms.conditions');
            Route::post('/update-term', [PolicyController::class, 'update_term'])->name('update.term');
        });

        Route::middleware(['permission:Categories Master'])->prefix('category')->name('category.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/table', [CategoryController::class, 'table'])->name('table');
            Route::post('/store', [CategoryController::class, 'store'])->name('store');
            Route::post('/update', [CategoryController::class, 'update'])->name('update');
            Route::delete('/category-delete/{id}', [CategoryController::class, 'destroy'])->name('delete');
        });

        // Sub Category Master (Categories Master permission hi use hota hai)
        Route::middleware(['permission:Categories Master'])->prefix('sub-category')->name('sub-category.')->group(function () {
            Route::get('/', [SubCategoryController::class, 'index'])->name('index');
            Route::get('/table', [SubCategoryController::class, 'table'])->name('table');
            Route::post('/store', [SubCategoryController::class, 'store'])->name('store');
            Route::post('/update', [SubCategoryController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [SubCategoryController::class, 'destroy'])->name('delete');
        });

        Route::middleware(['permission:Banner Master'])->prefix('banner')->name('banner.')->group(function () {
            Route::get('/', [BannerController::class, 'index'])->name('index');
            Route::get('/table', [BannerController::class, 'table'])->name('table');
            Route::post('/store', [BannerController::class, 'store'])->name('store');
            Route::post('/update', [BannerController::class, 'update'])->name('update');
            Route::delete('/banner-delete/{id}', [BannerController::class, 'destroy'])->name('delete');
        });

        Route::middleware(['permission:Popup Master'])->prefix('popup')->name('popup.')->group(function () {
            Route::get('/', [PopupController::class, 'index'])->name('index');
            Route::get('/table', [PopupController::class, 'table'])->name('table');
            Route::post('/store', [PopupController::class, 'store'])->name('store');
            Route::post('/update', [PopupController::class, 'update'])->name('update');
            Route::delete('/popup-delete/{id}', [PopupController::class, 'destroy'])->name('delete');
        });

        // Notification Master: sab members ke liye notification (CKEditor + Default + Active/Inactive)
        Route::middleware(['permission:Notification Master'])->prefix('notification-master')->name('notification-master.')->group(function () {
            Route::get('/', [NotificationMasterController::class, 'index'])->name('index');
            Route::get('/table', [NotificationMasterController::class, 'table'])->name('table');
            Route::post('/store', [NotificationMasterController::class, 'store'])->name('store');
            Route::post('/update', [NotificationMasterController::class, 'update'])->name('update');
            Route::post('/{notification}/default', [NotificationMasterController::class, 'toggleDefault'])->name('default');
            Route::post('/{notification}/status', [NotificationMasterController::class, 'toggleStatus'])->name('status');
            Route::delete('/{notification}', [NotificationMasterController::class, 'destroy'])->name('delete');
        });

        Route::middleware(['permission:Level Master'])->prefix('level')->name('level.')->group(function () {
            Route::get('/', [LevelController::class, 'index'])->name('index');
            Route::get('/table', [LevelController::class, 'table'])->name('table');
            Route::post('/store', [LevelController::class, 'store'])->name('store');
            Route::post('/update', [LevelController::class, 'update'])->name('update');
            Route::delete('/level-delete/{id}', [LevelController::class, 'destroy'])->name('delete');
        });

        Route::middleware(['permission:Member Details'])->prefix('member-details')->name('member.details')->group(function () {
            Route::get('/', [MemberDetailsController::class, 'index']);
            Route::get('table', [MemberDetailsController::class, 'table'])->name('.table');
            Route::get('export', [MemberDetailsController::class, 'export'])->name('.export');
            Route::get('create', [MemberDetailsController::class, 'create'])->name('.create');
            Route::post('store', [MemberDetailsController::class, 'store'])->name('.store');
            Route::get('edit/{id}', [MemberDetailsController::class, 'edit'])->name('.edit');
            Route::post('update/{id}', [MemberDetailsController::class, 'update'])->name('.update');
            Route::get('view/{id}', [MemberDetailsController::class, 'show'])->name('.show');
            Route::get('sponsor-check', [MemberDetailsController::class, 'sponsorCheck'])->name('.sponsor.check');
            Route::get('cities/{stateId}', [MemberDetailsController::class, 'cities'])->name('.cities');
            Route::post('status', [MemberDetailsController::class, 'updateStatus'])->name('.status');
            Route::post('profile-status', [MemberDetailsController::class, 'updateProfileStatus'])->name('.profile.status');
            Route::get('login/{uniqueId}', [MemberDetailsController::class, 'loginAsMember'])->name('.login');
            Route::delete('{id}', [MemberDetailsController::class, 'destroy'])->name('.delete');
        });

        Route::middleware(['permission:Admin Tree View'])->prefix('member')->name('member.')->group(function () {
            Route::get('tree-table', [MemberTreeController::class, 'treeTable'])->name('tree.table');
            Route::get('tree/{uniqueId?}', [MemberTreeController::class, 'tree'])->name('tree');
        });

        Route::middleware(['permission:Admin Downline'])->prefix('member')->name('member.')->group(function () {
            Route::get('downline-table', [MemberTreeController::class, 'downlineTable'])->name('downline.table');
            Route::get('downline/{uniqueId?}', [MemberTreeController::class, 'downline'])->name('downline');
        });

        Route::middleware(['permission:Product Management'])->prefix('product')->name('product.')->group(function () {
            Route::get('/list', [ProductAdminController::class, 'admin_product_list'])->name('list');
            Route::get('/table', [ProductAdminController::class, 'admin_product_table'])->name('table');
            Route::post('/approve', [ProductAdminController::class, 'admin_product_approve'])->name('approve');
            Route::post('/reject', [ProductAdminController::class, 'admin_product_reject'])->name('reject');
            Route::post('/delete', [ProductAdminController::class, 'admin_product_delete'])->name('delete');
            Route::get('/view/{id}', [ProductAdminController::class, 'admin_view_product'])->name('view');
        });

        Route::middleware(['permission:Blog Management'])->group(function () {
            Route::post('/blog/{blog}/toggle-status', [AdminBlogController::class, 'toggleStatus'])->name('blog.toggle-status');
            Route::resource('blog', AdminBlogController::class)->except(['show']);
        });

        Route::middleware(['permission:View Contact Inquiries'])->group(function () {
            Route::get('/contact-inquiries', [InquiryController::class, 'contactList'])->name('contact.inquiries.list');
            Route::post('/contact-inquiries/status-update', [InquiryController::class, 'contactStatusUpdate'])->name('contact.inquiries.status.update');
        });

        Route::middleware(['permission:View Product Inquiries'])->group(function () {
            Route::get('/product-inquiries', [InquiryController::class, 'productList'])->name('product.inquiries.list');
            Route::post('/product-inquiries/status-update', [InquiryController::class, 'productStatusUpdate'])->name('product.inquiries.status.update');
        });
        
        Route::prefix('survey')->name('survey.')->group(function () {
            Route::get('/', [SurveyAdminController::class, 'index'])->name('index');
            Route::get('create', [SurveyAdminController::class, 'create'])->name('create');
            Route::post('/', [SurveyAdminController::class, 'store'])->name('store');
            Route::get('{survey}/edit', [SurveyAdminController::class, 'edit'])->name('edit');
            Route::put('{survey}', [SurveyAdminController::class, 'update'])->name('update');
            Route::post('{survey}/status', [SurveyAdminController::class, 'status'])->name('status');
            Route::delete('{survey}', [SurveyAdminController::class, 'destroy'])->name('destroy');
            Route::get('{survey}/results', [SurveyAdminController::class, 'results'])->name('results');
    
            // Questions
            Route::get('{survey}/questions', [SurveyQuestionAdminController::class, 'index'])->name('questions.index');
            Route::get('{survey}/questions/create', [SurveyQuestionAdminController::class, 'create'])->name('questions.create');
            Route::post('{survey}/questions', [SurveyQuestionAdminController::class, 'store'])->name('questions.store');
            Route::get('{survey}/questions/{question}/edit', [SurveyQuestionAdminController::class, 'edit'])->name('questions.edit');
            Route::put('{survey}/questions/{question}', [SurveyQuestionAdminController::class, 'update'])->name('questions.update');
            Route::delete('{survey}/questions/{question}', [SurveyQuestionAdminController::class, 'destroy'])->name('questions.destroy');
        });
    });
});

Route::get('/login', [AuthMemberController::class, 'login_form'])->name('login')->middleware('guest');
Route::post('/login', [AuthMemberController::class, 'login_check'])->name('check.login')->middleware('throttle:10,1');
Route::get('/logout', [AuthMemberController::class, 'logout'])->name('logout');

Route::get('/register', [AuthMemberController::class, 'register_form'])->name('register');
Route::post('/register-submit', [AuthMemberController::class, 'register_submit'])->name('register.submit');
Route::get('/get-cities/{stateId}', [AuthMemberController::class, 'cities'])->name('get.cities');

Route::get('/check-sponsor-id/{id}', [AuthMemberController::class, 'checkSponsorId'])->name('check.sponsor');
Route::get('/check-email', [AuthMemberController::class, 'checkEmail'])->name('check.email');
Route::get('/check-phone', [AuthMemberController::class, 'checkPhone'])->name('check.phone');

Route::get('/forgot-passwords', [AuthMemberController::class, 'forgot_password'])->name('forgot.password');
Route::post('/check-forgot-password', [AuthMemberController::class, 'check_forgot_password'])->name('check.forgot.password')->middleware('throttle:5,1');
Route::post('/resend-forgot-otp', [AuthMemberController::class, 'resend_forgot_otp'])->name('resend.forgot.otp')->middleware('throttle:5,1');
Route::post('check-forgot-otp', [AuthMemberController::class, 'check_forgot_otp'])->name('check.forgot.otp')->middleware('throttle:10,1');
Route::get('update-password/{encryptedUserId}', [AuthMemberController::class, 'update_password'])->name('update.password');
Route::post('save-password', [AuthMemberController::class, 'save_password'])->name('save.password');

Route::get('Business-profile/{uniqueId}', [ProfileController::class, 'show'])->name('business.profile');
Route::get('/Refer', [AuthMemberController::class, 'register_form'])->name('refer');

Route::middleware(['auth:web'])->prefix('member')->name('member.')->group(function () {

    Route::middleware(['permission:Member Dashboard'])->group(function () {
        Route::get('/dashboard', [DashboardMemberController::class, 'dashboard'])->name('dashboard');
    });

    Route::middleware(['permission:Member Profile'])->group(function () {
        Route::get('/profile', [ProfileMemberController::class, 'profile'])->name('profile');
        Route::post('/personal-info-update', [ProfileMemberController::class, 'personal_info_update'])->name('personal.info.update');
        Route::post('/about-update', [ProfileMemberController::class, 'about_update'])->name('about.update');
        Route::post('/kyc-update', [ProfileMemberController::class, 'kyc_update'])->name('kyc.update');
        Route::post('/video-update', [ProfileMemberController::class, 'video_update'])->name('video.update');
        Route::post('/images-update', [ProfileMemberController::class, 'images_update'])->name('images.update');
        Route::post('/delete-image', [ProfileMemberController::class, 'delete_image'])->name('delete.image');
    });

    Route::middleware(['permission:Member Notifications'])->group(function () {
        Route::get('/notifications', [NotificationController::class, 'memberIndex'])->name('notification');
        Route::get('/notifications/latest', [NotificationController::class, 'latest'])->name('notifications.latest');
        Route::get('/notifications/{id}/open', [NotificationController::class, 'redirect'])->name('notifications.open');
        Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
        Route::delete('/notifications/clear-read', [NotificationController::class, 'clearRead'])->name('notifications.clearRead');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });

    //Products
    Route::middleware(['permission:Member Product Master'])->group(function () {
        Route::get('/products', [ProductMemberController::class, 'products'])->name('products');
        Route::get('/product-list', [ProductMemberController::class, 'product_list'])->name('product.list');
        Route::get('/product-create', [ProductMemberController::class, 'product_create'])->name('product.create');
        Route::post('/product-store', [ProductMemberController::class, 'product_store'])->name('product.store');
        Route::get('/product-edit/{id}', [ProductMemberController::class, 'product_edit'])->name('product.edit');
        Route::post('/product-update', [ProductMemberController::class, 'product_update'])->name('product.update');
        Route::post('/product-delete', [ProductMemberController::class, 'product_delete'])->name('product.delete');
        Route::post('/product-complete', [ProductMemberController::class, 'product_complete'])->name('product.complete');
    });

    Route::middleware(['permission:Member Product Inquiry'])->group(function () {
        Route::get('/products-inquiries', [ProductMemberController::class, 'products_inquiries_list'])->name('products.inquiries.list');
        Route::get('/products-inquiries/product/{productId}', [ProductMemberController::class, 'products_inquiries_by_product'])->whereNumber('productId')->name('products.inquiries.product');
        Route::post('/products/inquiries/status-update', [ProductMemberController::class, 'products_inquiry_status_update'])->name('products.inquiries.status.update');
    });

    Route::middleware(['permission:Member Tree View'])->group(function () {
        Route::get('/treeview', [MLMController::class, 'treeview'])->name('treeview');
    });

    Route::middleware(['permission:My Downline'])->group(function () {
        Route::get('/my-downline', [MLMController::class, 'my_downline'])->name('my.downline');
    });
});

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('about', [PageController::class, 'about'])->name('about');
Route::get('contact-us', [PageController::class, 'contact'])->name('contact');
Route::post('contact-us', [PageController::class, 'sendContact'])->name('contact.send')->middleware('throttle:5,1');

Route::get('i-want', [ProductController::class, 'want'])->name('products.want');
Route::get('i-have', [ProductController::class, 'have'])->name('products.have');
Route::get('search', [ProductController::class, 'search'])->name('products.search');
Route::get('product/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('product/{slug}/enquiry', [ProductInquiryController::class, 'store'])->middleware('throttle:5,1')->name('products.enquiry');

Route::get('profile/{uniqueId}', [ProfileController::class, 'show'])->name('profile.show');

Route::get('category', [FrontCategoryController::class, 'index'])->name('category.index');
Route::get('category/{slug}', [FrontCategoryController::class, 'show'])->name('category.show');
Route::get('category/{slug}/{subSlug}', [FrontCategoryController::class, 'show'])->name('category.sub');

Route::get('blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('survey', [SurveyController::class, 'show'])->name('survey.show');
Route::post('survey', [SurveyController::class, 'submit'])->middleware(['auth:web', 'throttle:10,1'])->name('survey.submit');