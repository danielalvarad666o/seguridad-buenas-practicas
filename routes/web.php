<?php
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use App\Http\Controllers\user\usuarioController;
use Illuminate\Cache\RateLimiter;
use App\Http\Controllers\sms;
use Illuminate\Http\Request;

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

// Route::get('/', function () {
//     return view('auth.register'); // Redirige directamente a la vista de registro
// });
Auth::routes();




// Rutas para registro y login
Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('s-Registro'); // Redirige directamente a la vista de registro
    });
    
    Route::post('/registroUsuario', [usuarioController::class, 'crearUser'])->name('registroS');


    Route::post('/smsVerificacion', [usuarioController::class, 'verificarUser'])->name('smsVerificacion');
    
    Route::post('/iniciarsession', [usuarioController::class, 'login'])->name('iniciarsession');
    
    Route::get('/login', function () {
        return view('s-login');
    })->name('login');
 });

// Ruta de inicio con middleware Sanctum
Route::middleware(['auth:sanctum','throttle'])->group(function () {
    Route::get('/inicio', function () {
        return view('s-home');
    })->name('inicio');


    Route::get('/logout', [usuarioController::class, 'logout'])->name('logout');
});

// Otras rutas
Route::get('/vistaCode/{userId}', [usuarioController::class, 'vistaCode'])->name('code')->middleware('admin','signed','throttle');
Route::match(['get', 'post'], 'smsNew/{userId}', [usuarioController::class, 'resetSMS'])->name('smsNew')->middleware('checkRole:administrador','throttle');;


