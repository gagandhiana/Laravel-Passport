# Laravel passport
APIs typically use tokens to authenticate users because APIs do not maintain session state between requests.Laravel Passport provides a full OAuth2 server implementation for your Laravel application in a matter of minutes.
## We will get here
### Register API
### Details API
### Login API
# Getting Started
## Step 1: Install Package
~~~~
composer require laravel/passport
~~~~
## Step 2: config/auth.php
~~~~
'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
        ],
    ],
~~~~
## Step 3: config/app.php file and add service provider
~~~~
'providers' =>[
Laravel\Passport\PassportServiceProvider::class,
],
~~~~
## Step 4: Run Migration and Install
~~~~
php artisan migrate
~~~~
~~~~
php artisan passport:install
~~~~
## Step 5: Passport Configuration app/Models/User.php
~~~~
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
~~~~
## Step 6: app/Providers/AuthServiceProvider.php
~~~~
<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',  
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Passport::loadKeysFrom(__DIR__.'/../secrets/oauth');
        Passport::ignoreRoutes();
    }
}
~~~~
## Step 7: Create API Route
~~~~
Route::post('register', 'App\Http\Controllers\API\UserController@register');

Route::group(['middleware' => 'auth:api'], function(){
Route::post('details', 'App\Http\Controllers\API\UserController@details');
});

Route::post('login', 'App\Http\Controllers\API\UserController@login'); 
~~~~
## Step 8: Create the Controller
~~~~
<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;

class UserController extends Controller
{

    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'email' => 'required|email', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        $success['token'] =  $user->createToken('MyApp')-> accessToken; 
        $success['name'] =  $user->name;
        return response()->json(['success'=>$success], $this-> successStatus); 
    }

    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    }
    
    public function login(){ 
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')-> accessToken; 
            return response()->json(['success' => $success], $this-> successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
}
~~~~
## Step 9: Run
~~~~
php artisan serve
~~~~
