<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\CatchmentController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\MunicipalityController;
use App\Http\Controllers\WzController;
use App\Models\Job;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Tutaj rejestrujesz trasy swojej aplikacji.
|
*/

// Trasy dla zalogowanych użytkowników
Route::middleware('auth')->group(function () {

    Route::get('/', [HomeController::class, 'home'])->name('home');

    Route::get('dashboard', function () {
        $jobs = Job::all();
        return view('dashboard', ['jobs' => $jobs]);
    })->name('admin.dashboard');

    Route::get('billing', function () {
        return view('billing');
    })->name('billing');

    Route::get('profile', function () {
        return view('profile');
    })->name('profile');

	 // Trasy dla dokumentów billingowych (WZ, Invoice, PK)
	 Route::get('/billing', [\App\Http\Controllers\BillingController::class, 'index'])->name('billing.index');
	 Route::get('/billing/create', [\App\Http\Controllers\BillingController::class, 'create'])->name('billing.create');
	 Route::post('/billing', [\App\Http\Controllers\BillingController::class, 'store'])->name('billing.store');
	 Route::get('/billing/{billing}', [\App\Http\Controllers\BillingController::class, 'show'])->name('billing.show');
	 Route::get('/billing/{billing}/edit', [\App\Http\Controllers\BillingController::class, 'edit'])->name('billing.edit');
	 Route::put('/billing/{billing}', [\App\Http\Controllers\BillingController::class, 'update'])->name('billing.update');
	 Route::delete('/billing/{billing}', [\App\Http\Controllers\BillingController::class, 'destroy'])->name('billing.destroy');
 
	 // Trasy dla dodawania płatności – osobny model Payment
	 Route::get('/billing/{wz}/add-payment', [\App\Http\Controllers\PaymentController::class, 'addPaymentForm'])->name('payments.addPaymentForm');
	 Route::post('/billing/{wz}/add-payment', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');

    // Zarządzanie użytkownikami
    Route::get('/user-management', [UsersController::class, 'read'])->name('user-management');
    Route::get('/user/new', [UsersController::class, 'create'])->name('create-user');
    Route::post('/user/new', [UsersController::class, 'save'])->name('save-user');
    Route::get('/user/{id}', [UsersController::class, 'show'])->name('user');
    Route::put('/user', [UsersController::class, 'update']);

    Route::get('/drivers', [UsersController::class, 'drivers'])->name('drivers');
    Route::get('/driver/new', [UsersController::class, 'createDriver'])->name('create-driver');
    Route::post('/driver/new', [UsersController::class, 'saveDriver'])->name('save-driver');

    // Adresy
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses');
    Route::get('/address/new', [AddressController::class, 'create'])->name('create-address');
    Route::post('/address/new', [AddressController::class, 'store'])->name('save-address');
    Route::get('/address/{id}', [AddressController::class, 'show'])->name('address');
    Route::put('/address', [AddressController::class, 'update']);
    Route::post('/address/delete', [AddressController::class, 'destroy']);

    // Zlecenia
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs');
    Route::get('/moje-zlecenia', [JobController::class, 'index_client'])->name('client_jobs');
    Route::post('/job', [JobController::class, 'update']);
    Route::get('/job/new', [JobController::class, 'create'])->name('create-job');
    Route::get('/job/{id}', [JobController::class, 'show'])->name('job');
    Route::post('/job/new', [JobController::class, 'store']);
    Route::post('/job/delete', [JobController::class, 'destroy']);

    // WZ
    Route::get('/wz', [WzController::class, 'index'])->name('wzs');
    Route::get('/wz-create', [WzController::class, 'create'])->name('create-wz');
    Route::post('/wz-create', [WzController::class, 'save']);
    Route::get('/wz/{id}', [WzController::class, 'show'])->name('wz');
    Route::get('/wz-send/{id}', [WzController::class, 'send'])->name('wz-send');
    Route::get('/wz-download/{id}', [WzController::class, 'download'])->name('wz-download');
    Route::post('/wz-save', [WzController::class, 'update']);

    // Ciężarówki
    Route::get('/trucks', [TruckController::class, 'index'])->name('trucks');
    Route::get('/truck/new', [TruckController::class, 'create'])->name('create-truck');
    Route::post('/truck/new', [TruckController::class, 'store']);
    Route::post('/truck/delete', [TruckController::class, 'destroy']);

    // Strefy (Zones)
    Route::get('/zones', [ZoneController::class, 'index'])->name('zones');
    Route::get('/zone/new', [ZoneController::class, 'create'])->name('create-zone');
    Route::post('/zone/new', [ZoneController::class, 'store']);
    Route::get('/zone/{id}', [ZoneController::class, 'show'])->name('zone');
    Route::put('/zone', [ZoneController::class, 'update']);
    Route::post('/zone/delete', [ZoneController::class, 'destroy']);

    // Gminy
    Route::get('/municipalities', [MunicipalityController::class, 'index'])->name('municipalities');
    Route::get('/municipality/new', [MunicipalityController::class, 'create'])->name('create-municipality');
    Route::post('/municipality/new', [MunicipalityController::class, 'store']);
    Route::post('/municipality/delete', [MunicipalityController::class, 'destroy']);

    // Obszary poboru (Catchments)
    Route::get('/catchments', [CatchmentController::class, 'index'])->name('catchments');
    Route::get('/catchment/new', [CatchmentController::class, 'create'])->name('create-catchment');
    Route::post('/catchment/new', [CatchmentController::class, 'store']);
    Route::post('/catchment/delete', [CatchmentController::class, 'destroy']);

    // Prace (Work)
    Route::get('/work', [WorkController::class, 'jobs'])->name('work');
    Route::post('/work/processPump', [WorkController::class, 'processPump'])->name('work.processPump');
    Route::post('/work/processDump', [WorkController::class, 'processDump'])->name('work.processDump');
    Route::post('/work/updateJobStatus', [WorkController::class, 'updateJobStatus'])->name('work.updateJobStatus');
    Route::get('/work/select', [WorkController::class, 'select'])->name('work.select');
    Route::get('/work/select/{id}', [WorkController::class, 'jobs_select'])->name('work.jobs_select');
    Route::post('/work/assignJobDriver', [WorkController::class, 'assignJobDriver'])->name('work.assignJobDriver');

    Route::get('/daily_report', [JobController::class, 'daily'])->name('daily_report');
    Route::post('/generate_report', [JobController::class, 'generate'])->name('generate_report');

    Route::get('/done_report', [JobController::class, 'done_report'])->name('done_report');
    Route::post('/generate_done_report', [JobController::class, 'generate_done_report'])->name('generate_done_report');

    // Dodatkowe strony
    Route::get('/sms', function () {
        return view('sms/settings');
    });

    Route::get('/faktury', function () {
        return view('billing');
    })->name('faktury');

    Route::get('tables', function () {
        return view('tables');
    })->name('tables');

    Route::get('static-sign-in', function () {
        return view('static-sign-in');
    })->name('sign-in');

    Route::get('static-sign-up', function () {
        return view('static-sign-up');
    })->name('sign-up');

    Route::get('/logout', [SessionsController::class, 'destroy'])->name('logout');
    Route::get('/user-profile', [InfoUserController::class, 'create'])->name('user-profile');
    Route::post('/user-profile', [InfoUserController::class, 'store']);
});

// Trasy dla użytkowników niezalogowanych
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [SessionsController::class, 'create'])->name('login');
    Route::post('/session', [SessionsController::class, 'store']);

    Route::get('/login/forgot-password', [ResetController::class, 'create'])->name('forgot-password');
    Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});
