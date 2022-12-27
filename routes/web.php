<?php

use App\Models\User;
use App\Models\Client;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClerkController;
use App\Http\Controllers\Common\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Auth;

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

Route::get('login', [LoginController::class, 'create'])->name('login');
Route::post('login', [LoginController::class, 'store']);
Route::post('logout', [LoginController::class, 'destroy'])->middleware('auth:admin');

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
});
Route::post('/register', function () {
    // validate the request
    $attributes = Request::validate([
        'name' => 'required',
        'email' => ['required', 'email'],
        'password' => 'required',
    ]);
    // create the user
    User::create($attributes);
    // redirect
    return redirect('/login');
});

Route::middleware('admin')->group(function () {
    
    Route::get('/lawyers', function () {
        return Inertia::render('Admin/Lawyers/Index', [
            'users' => User::query()
                ->when(Request::input('search'), function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->withQueryString()
                ->through(fn($user) => [
                    'id' => $user->id,
                    'name' => $user->name
                ]),
            'filters' => Request::only(['search']),
        ]);
    });
    
    Route::get('/lawyers/create', function () {
        return Inertia::render('Admin/Lawyers/Create');
    });

    Route::get('/lawyers/:id/edit', function () {
        return Inertia::render('Admin/Lawyers/Edit');
    });
    
    Route::post('/lawyers', function () {
        // validate the request
        $attributes = Request::validate([
            'name' => 'required',
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);
        // create the user
        User::create($attributes);
        // redirect
        return redirect('/lawyers');
    });

    Route::resource('clerks', ClerkController::class)->except('show');
    // Route::get('/clerks', function () {
    //     return Inertia::render('Admin/Clerks');
    // });

    Route::resource('company-profile', CompanyController::class)->except(['show', 'edit', 'destroy']);
    Route::get('company-profile/edit', [CompanyController::class, 'edit'])->name('company-profile.edit');
    // Route::get('/company-profile', function () {
    //     return Inertia::render('Admin/CompanyProfile');
    // });
    
    Route::get('/settings', function () {
        $userId = Auth::id();
        return Inertia::render('Admin/Settings', [$userId]);
    });
    
    // Route::post('/logout', function () {
    //     dd('logging out');
    // });

});


//Common routes for all users
Route::middleware('is.valid.user')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

// Route::get('/clients', [ClientController::class, 'index']);
Route::middleware('lawyer')->group(function () {
    Route::resource('clients', ClientController::class);

    // Route::get('/clients', function () {
        
    // });
    // Route::get('/clients/create', function () {
    //     return Inertia::render('Lawyer/Client/Create');
    // });

    // Route::get('/clients/:id/edit', function () {
    //     return Inertia::render('Lawyer/Client/Edit');
    // });
    // Route::post('/clients', function () {
    //     // validate the request
    //     $attributes = Request::validate([
    //         'name' => 'required',
    //         'id_types_id'=>'required',
    //         'id_num'=>'required',
    //         'email' => ['required', 'email'],
    //         'phone_number'=>'required',
    //         'address' => 'required',
    //         'created_by'=>'nullable',
    //     ]);
    //     // create the user
    //     Client::create($attributes);
    //     // redirect
    //     return redirect('/clients');
    // });
});