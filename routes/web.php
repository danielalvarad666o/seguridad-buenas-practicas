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

Route::get('/', function () {
    return view('s-Registro'); // Redirige directamente a la vista de registro
});



//  Route::get('/code/{userId}', [registroController::class, 'sms'])->name('code');

Route::get('/vistaCode/{userId}', [usuarioController::class, 'vistaCode'])->name('code');




// Route::get('/sms/{userId}', function ($userId) { return view('s-code', ['userId' => $userId]); })->name('code');

Route::post('/registroUsuario', [usuarioController::class, 'crearUser'])->name('registroS');

Route::post('/smsVerificacion', [usuarioController::class, 'verificarUser'])->name('smsVerificacion');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::match(['get', 'post'], '{userId}', [usuarioController::class, 'resetSMS'])->name('smsNew');



