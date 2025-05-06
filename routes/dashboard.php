<?php

use App\Http\Controllers\dashboard\MoreCheckController;
use App\Models\dashboard\CheckText;
use App\Models\dashboard\SpeedDevice;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\AdminController;
use App\Http\Controllers\dashboard\RolesController;
use App\Http\Controllers\dashboard\InvoiceController;
use App\Http\Controllers\dashboard\MessageController;
use App\Http\Controllers\dashboard\SettingController;
use App\Http\Controllers\dashboard\WelcomeController;
use App\Http\Controllers\dashboard\auth\AuthController;
use App\Http\Controllers\dashboard\CheckTextController;
use App\Http\Controllers\dashboard\SpeedDeviceController;
use App\Http\Controllers\dashboard\NotificationController;
use App\Http\Controllers\dashboard\TechInvoicesController;
use App\Http\Controllers\dashboard\PublicInvoiceController;
use App\Http\Controllers\dashboard\ProgrameDeviceController;
use App\Http\Controllers\dashboard\ProblemCategoryController;
use App\Http\Controllers\dashboard\auth\ResetPasswordController;
use App\Http\Controllers\dashboard\auth\ForgetPasswordController;
use App\Http\Controllers\dashboard\PieceResourceController;
use App\Http\Controllers\dashboard\ProgrameProblemCategoryController;
use App\Http\Controllers\dashboard\SpeedProblemCategoryController;

Route::group([
    'prefix' => '/dashboard',
    'as' => 'dashboard.',
], function () {

    ##################### Auth Login Controller  ########################
    Route::controller(AuthController::class)->group(function () {
        Route::get('login', 'show_login')->name('login.show');
        Route::post('register_login', 'register_login');
        Route::post('logout', 'logout')->name('logout');
    });

    ############################### End Auth Login Controller ###############
    ############################## Public Invoice Controller ###############
    Route::controller(PublicInvoiceController::class)->group(function () {
        Route::get('invoice/view/{id}', 'PublicInvoice')->name('show_invoice');
    });
    Route::view('terms', 'dashboard.terms')->name('terms');
    ############################## End Public Invoice Controller ############
    ################### Reset Password #############
    Route::controller(ForgetPasswordController::class)->group(function () {
        Route::get('password/email', 'showemailform')->name('password.email');
        Route::post('password/email', 'sendotp')->name('password.email.post');
        Route::get('password/verify/{email}', 'showotpform')->name('password.otp.show');
        Route::get('password/verify', 'otpverify')->name('password.otp.post');
        Route::match(['post', 'get'], 'forget-password', 'forget_password')->name('forget_password');
        Route::match(['post', 'get'], 'change-forget-password/{code}', 'change_forget_password');
        Route::post('user/update_forget_password', 'update_forget_password')->name('update_forget_password');
    });
    Route::controller(ResetPasswordController::class)->group(function () {
        Route::get('password/reset/{email}', 'ShowResetForm')->name('password.reset');
        Route::post('password/reset', 'resetpassword')->name('password.reset.post');

    });

    ############################### Start Admin Auth Route  ###############
    Route::group(['middleware' => 'auth:admin'], function () {
        Route::controller(AuthController::class)->group(function () {
            Route::match(['post', 'get'], 'update_profile', 'update_profile')->name('update_profile');
            Route::match(['post', 'get'], 'update_password', 'update_password')->name('update_password');
        });

        ############################### Start Welcome  Controller ###############

        Route::controller(WelcomeController::class)->group(function () {
            Route::get('welcome', 'index')->name('welcome');
        });

        ############################### End  Welcome  Controller ###############
        ##################### Start Role Permissions ####################
        Route::group(['middleware' => 'can:roles', 'prefix' => 'role', 'as' => 'roles.'], function () {
            Route::controller(RolesController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::match(['get', 'post'], 'create', 'create')->name('create');
                // Route::post('store', 'store')->name('store')->middleware('can:roles');
                Route::match(['get', 'post'], 'update/{id}', 'update')->name('update');
                Route::post('destroy/{id}', 'destroy')->name('destroy');
            });
        });

        ##################### End Role Permissions #########################

        ##################### Start Admins Routes #########################
        Route::group(['middleware' => 'can:admins', 'prefix' => 'admins', 'as' => 'admins.'], function () {
            Route::controller(AdminController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::get('tech', 'tech')->name('tech');
                Route::post('update_tech/{id}', 'update_tech')->name('update_tech');
                Route::match(['get', 'post'], 'create', 'create')->name('create');
                Route::match(['post', 'get'], 'update/{id}', 'update')->name('update');
                Route::post('destroy/{id}', 'destroy')->name('destroy');
                ######################### Show Tech Invoices  Admins ############################
                Route::match(['post', 'get'], 'tech_invoices/{id}', 'tech_invoices')->name('tech_invoices');
            });
        });
        ################### End Admins Routes ###########################
        ###################### Start Problem Category #################
        Route::group(['middleware' => 'can:problem_categories', 'prefix' => 'problem_categories', 'as' => 'problem_categories.'], function () {
            Route::controller(ProblemCategoryController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::match(['get', 'post'], 'create', 'create')->name('create');
                Route::match(['post', 'get'], 'update/{id}', 'update')->name('update');
                Route::post('destroy/{id}', 'destroy')->name('destroy');
            });
        });
        ##################### End Problem Category ###################


        ###################### Start Problem Category Programe Device  #################
        Route::group(['middleware' => 'can:problem_categories', 'prefix' => 'programe_problem_categories', 'as' => 'programe_problem_categories.'], function () {
            Route::controller(ProgrameProblemCategoryController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::match(['get', 'post'], 'create', 'create')->name('create');
                Route::match(['post', 'get'], 'update/{id}', 'update')->name('update');
                Route::post('destroy/{id}', 'destroy')->name('destroy');
            });
        });
        ###################### End  Problem Category Programe Device  #################

        ###################### Start Problem Category Speed Device  #################
        Route::group(['middleware' => 'can:problem_categories', 'prefix' => 'speed_problem_categories', 'as' => 'speed_problem_categories.'], function () {
            Route::controller(SpeedProblemCategoryController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::match(['get', 'post'], 'create', 'create')->name('create');
                Route::match(['post', 'get'], 'update/{id}', 'update')->name('update');
                Route::post('destroy/{id}', 'destroy')->name('destroy');
            });
        });
        ###################### End  Problem Category Speed Device  #################


        ###################### Start Check Text  #################
        Route::group(['middleware' => 'can:problem_categories', 'prefix' => 'check_text', 'as' => 'check_text.'], function () {
            Route::controller(CheckTextController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::match(['get', 'post'], 'create', 'create')->name('create');
                Route::match(['post', 'get'], 'update/{id}', 'update')->name('update');
                Route::post('destroy/{id}', 'destroy')->name('destroy');
            });
        });
        ##################### End Check Text  ###################
        ###################### Start Speed Device   #################
        Route::group(['middleware' => 'can:problem_categories', 'prefix' => 'speed_device', 'as' => 'speed_device.'], function () {
            Route::controller(SpeedDeviceController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::match(['get', 'post'], 'create', 'create')->name('create');
                Route::match(['post', 'get'], 'update/{id}', 'update')->name('update');
                Route::post('destroy/{id}', 'destroy')->name('destroy');
            });
        });
        ##################### End Speed Device   ###################
        ###################### Start Programe Device   #################
        Route::group(['middleware' => 'can:problem_categories', 'prefix' => 'programe_device', 'as' => 'programe_device.'], function () {
            Route::controller(ProgrameDeviceController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::match(['get', 'post'], 'create', 'create')->name('create');
                Route::match(['post', 'get'], 'update/{id}', 'update')->name('update');
                Route::post('destroy/{id}', 'destroy')->name('destroy');
            });
        });
        ##################### End Programe Device  ###################
        ###################### Start PieceResource Controller   #################
        Route::group(['middleware' => 'can:admins', 'prefix' => 'piece_resource', 'as' => 'piece_resource.'], function () {
            Route::controller(PieceResourceController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::match(['get', 'post'], 'create', 'create')->name('create');
                Route::match(['post', 'get'], 'update/{id}', 'update')->name('update');
                Route::post('destroy/{id}', 'destroy')->name('destroy');
            });
        });
        ##################### End PieceResource Controller ###################
        ###################### Start MoreCheck Controller   #################
        Route::group(['middleware' => 'can:admins', 'prefix' => 'more_check', 'as' => 'more_check.'], function () {
            Route::controller(MoreCheckController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::match(['get', 'post'], 'create', 'create')->name('create');
                Route::match(['post', 'get'], 'update/{id}', 'update')->name('update');
                Route::post('destroy/{id}', 'destroy')->name('destroy');
            });
        });
        ##################### End MoreCheck Controller ###################
        ################### Start Invoices #######################
        Route::group(['middleware' => 'can:invoices', 'prefix' => 'invoices', 'as' => 'invoices.'], function () {
            Route::controller(InvoiceController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::match(['get', 'post'], 'create', 'create')->name('create');
                Route::match(['post', 'get'], 'update/{id}', 'update')->name('update');
                Route::post('destroy/{id}', 'destroy')->name('destroy');
                Route::get('invoice-haif-time', 'InvoicesHaifTime')->name('invoice-haif-time')->middleware('can:tech_invoices');
                Route::post('delete_file/{id}', 'delete_file')->name('delete_file');
                Route::get('print/{id}', 'print')->name('print');
                Route::get('print_barcode/{id}', 'print_barcode')->name('print_barcode');
                Route::get('steps/{id}', 'steps')->name('steps');
                Route::post('add_tech/{id}', 'add_tech')->name('add_tech');
                Route::post('delivery/{id}', 'delivery')->name('delivery');
            });
        });

        Route::group(['middleware' => 'can:tech_invoices', 'prefix' => 'invoices', 'as' => 'invoices.'], function () {
            Route::controller(InvoiceController::class)->group(function () {
                Route::get('invoice-haif-time', 'InvoicesHaifTime')->name('invoice-haif-time');
            });
        });

        ################# End Invoices #######################
        ################## Start Tech Invoices ###############
        Route::group(['middleware' => 'can:tech_invoices', 'prefix' => 'tech_invoices', 'as' => 'tech_invoices.'], function () {
            Route::controller(TechInvoicesController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::get('search', 'search')->name('search');
                Route::get('available', 'available')->name('available');
                Route::get('show/{id}', 'show')->name('show');
                Route::match(['get', 'post'], 'checkout/{id}', 'checkout')->name('checkout');
                // Route::post('checkout/{id}', 'checkout')->name('checkout');
                Route::match(['post', 'get'], 'update/{id}', 'update')->name('update');
                Route::post('addfile/{id}', 'AddFile')->name('addfile');
                Route::post('client-connect/{id}', 'ClientConnect')->name('client-connect');
            });
        });
        ################# End Tech Invoices #####################
        ################# Start Messages #####################
        Route::group(['middleware' => 'can:admins', 'prefix' => 'messages', 'as' => 'messages.'], function () {
            Route::controller(MessageController::class)->group(function () {
                Route::get('index', 'index')->name('index');
                Route::match(['post', 'get'], 'update/{id}', 'update')->name('update');
            });
        });
        ################# End Messages ######################
        ################ Start Notification Controller ############
        Route::controller(NotificationController::class)->group(function () {
            Route::get('all_read', 'AllRead')->name('all_read');
        });
        ################ End Notification Controller ##############
        ################# Start Setting Controller ###############
        Route::controller(SettingController::class)->group(function () {
            Route::get('setting', 'index')->name('setting.index');
            Route::post('setting/update', 'update')->name('setting.update');
        });
        ################# End Setting Controller ###############
    });


});
