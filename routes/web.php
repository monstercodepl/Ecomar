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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => 'auth'], function () {

    Route::get('/', [HomeController::class, 'home']);
	Route::get('dashboard', function () {
		$jobs = Job::all();

		return view('dashboard', ['jobs' => $jobs]);
	})->name('Admin');

	Route::get('billing', function () {
		return view('billing');
	})->name('billing');

	Route::get('profile', function () {
		return view('profile');
	})->name('profile');

	Route::get('/user-management', [UsersController::class, 'read'])->name('user-management');
	Route::get('/user/new', [UsersController::class, 'create'])->name('create-user');
	Route::post('/user/new', [UsersController::class, 'save'])->name('save-user');
	Route::get('/user/{id}', [UsersController::class, 'show'])->name('user');
	Route::put('/user', [UsersController::class, 'update']);

	Route::get('/drivers', [UsersController::class, 'drivers'])->name('drivers');
	Route::get('/driver/new', [UsersController::class, 'createDriver'])->name('create-driver');
	Route::post('/driver/new', [UsersController::class, 'saveDriver'])->name('save_driver');

	Route::get('/addresses', [AddressController::class, 'index'])->name('addresses');
	Route::get('/address/new', [AddressController::class, 'create'])->name('create-address');
	Route::post('/address/new', [AddressController::class, 'store'])->name('save-address');
	Route::get('/address/{id}', [AddressController::class, 'show'])->name('address');
	Route::put('/address', [AddressController::class, 'update']);
	Route::post('/address/delete', [AddressController::class, 'destroy']);

	Route::get('/jobs', [JobController::class, 'index'])->name('jobs');
	Route::get('/moje-zlecenia', [JobController::class, 'index_client'])->name('client_jobs');
	Route::post('/job', [JobController::class, 'update']);
	Route::get('/job/new', [JobController::class, 'create'])->name('create-job');
	Route::get('/job/{id}', [JobController::class, 'show'])->name('job');
	Route::post('/job/new', [JobController::class, 'store']);
	Route::post('/job/delete', [JobController::class, 'destroy']);
	Route::get('/wz/download/{job}', [WzController::class, 'download'])->name('wz.download');


	Route::get('/trucks', [TruckController::class, 'index'])->name('trucks');
	Route::get('/truck/new', [TruckController::class, 'create'])->name('create-truck');
	Route::post('/truck/new', [TruckController::class, 'store']);
	Route::post('/truck/delete', [TruckController::class, 'destroy']);

	Route::get('/zones', [ZoneController::class, 'index'])->name('zones');
	Route::get('/zone/new', [ZoneController::class, 'create'])->name('create-zone');
	Route::post('zone/new', [ZoneController::class, 'store']);
	Route::get('/zone/{id}', [ZoneController::class, 'show'])->name('zone');
	Route::put('/zone', [ZoneController::class, 'update']);
	Route::post('/zone/delete', [ZoneController::class, 'destroy']);

	Route::get('/municipalities', [MunicipalityController::class, 'index'])->name('municipalities');
	Route::get('/municipality/new', [MunicipalityController::class, 'create'])->name('create-municipality');
	Route::post('/municipality/new', [MunicipalityController::class, 'store']);
	Route::post('/municipality/delete', [MunicipalityController::class, 'destroy']);

	Route::get('/catchments', [CatchmentController::class, 'index'])->name('catchments');
	Route::get('/catchment/new', [CatchmentController::class, 'create'])->name('create-catchment');
	Route::post('/catchment/new', [CatchmentController::class, 'store']);
	Route::post('/catchment/delete', [CatchmentController:: class, 'destroy']);

	Route::get('/work', [WorkController::class, 'jobs'])->name('work');
	Route::post('/work/pump', [WorkController::class, 'pump']);
	Route::post('/work/dump', [WorkController::class, 'dump']);
	Route::post('/work/status', [WorkController::class, 'status']);

	Route::get('/daily_report', [JobController::class, 'daily'])->name('daily_report');
	Route::post('/generate_report', [JobController::class, 'generate'])->name('generate_report');

	Route::get('/done_report', [JobController::class, 'done_report'])->name('done_report');
	Route::post('/generate_done_report', [JobController:: class, 'generate_done_report'])->name('geenerate_done_reprot');

	Route::get('/sms', function () {
		return view('/sms/settings');
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

    Route::get('/logout', [SessionsController::class, 'destroy']);
	Route::get('/user-profile', [InfoUserController::class, 'create']);
	Route::post('/user-profile', [InfoUserController::class, 'store']);
    Route::get('/login', function () {
		return view('dashboard');
	})->name('sign-up');
});



Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

});

Route::get('/login', function () {
    return view('session/login-session');
})->name('login');