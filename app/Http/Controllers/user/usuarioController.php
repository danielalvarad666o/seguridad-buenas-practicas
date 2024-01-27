<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Cache\RateLimiting\Limit;
use App\Jobs\sms;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Hashids\Hashids;


class usuarioController extends Controller
{
    //
       //


       public function crearUser(Request $request)
       {
       
           try {
               //validar las petriciones
               $key = 'registro-' . $request->ip();
               $maxAttempts = 2;
               $decayMinutes = 1;
               $limiter = app(RateLimiter::class);
               
               if ($limiter->tooManyAttempts($key, $maxAttempts)) {
                   $seconds = $limiter->availableIn($key);
                   throw new ThrottleRequestsException('Demasiados intentos. Por favor, inténtelo de nuevo en ' . $seconds . ' segundos.');
               }
               //validacion recaptcha
               $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                   'secret' => env('RECAPTCHA_SECRET'),
                   'response' => $request->input('g-recaptcha-response')
               ])->object();
       
               
               if ($response->success == true && $response->score >= 0.7) {
       
                   //validator
                   $validacion = Validator::make($request->all(), [
                       'name' => ['required', 'string', 'max:50'],
                       'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                       'password' => ['required', 'string', 'min:8', 'confirmed'],
                       'phone' => ['required', 'min:10', 'max:10', 'unique:users,phone'],
                   ], [
                       'name.required' => 'El campo nombre es obligatorio.',
                       'name.string' => 'El campo nombre debe ser con puras letras.',
                       'name.max' => 'El campo nombre no puede exceder los 50 caracteres.',
                       'email.required' => 'El campo correo electrónico es obligatorio.',
                       'email.string' => 'El campo correo electrónico debe ser una cadena de caracteres.',
                       'email.email' => 'El formato del correo electrónico no es válido.',
                       'email.max' => 'El campo correo electrónico no puede exceder los 255 caracteres.',
                       'email.unique' => 'El correo electrónico ya está registrado.',
                       'password.required' => 'El campo contraseña es obligatorio.',
                       'password.string' => 'El campo contraseña debe ser una cadena de caracteres.',
                       'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
                       'password.confirmed' => 'La confirmación de la contraseña no coincide.',
                       'phone.required'=> 'el campo telefono es obligatorio. ',
                       'phone.max'=>'el campo que telefono debe tener 10 digitos . ',
                       'phone.min'=>'el campo que telefono debe tener 10 digitos ',
                       'phone.unique' => 'El telefono ya está registrado.',
       
                   ]);
       
                   if ($validacion->fails()) {
                       return redirect('/')
                           ->withErrors($validacion)
                           ->withInput();
                   }
       
       
                   srand (time());
                   $numero_aleatorio= rand(5000,6000);
       
       
                   //checar cual si ahi un registro si no lo hacemos admi 
                   $users = User::all();
                   $variableResultado = $users->isEmpty() ? 1 : 2;
                   
                   
       
                       $user = User::create([
                           'name' => $request->name,
                           'email' => $request->email,
                           'password' => Hash::make($request->password),
                           'phone' => $request->phone,
                           'rol_id'=>$variableResultado,
                           'code' => $numero_aleatorio,
                       ]);
                   
                       
                       
                   
                   
                   if ($user->save()){
                  #sms::dispatch($request->phone,$numero_aleatorio)->onQueue('sms')->onConnection('database')->delay(now()->addSeconds(2));
                   session()->flash('mensaje', '¡Usuario registrado correctamente!');
                     
           $url = URL::temporarySignedRoute(
               'code',
               now()->addMinutes(5),
               ['userId' => $user->id]
           );
       
           
           return redirect()->to($url);
               
                   
       
       
       
                   }else{
                       session()->flash('error', '¡El uauario no se pudo guardar intentelo de nuevo!');
                   }
       
                   
                   
               } else {
                   session()->flash('error', 'Erros de sistema. Por favor, inténtelo de nuevo.');
               }
           } catch (\Exception $e) {
               // Capturar y manejar la excepción, puedes loguearla, enviar notificaciones, etc.
               session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo.');
               
               
           }
       
           return redirect('/');
       }
       
       
       public function resetSMS($userId)
       {
           // Utilizar $userId en tu lógica
       
       
           $user = User::find($userId);
           
       
           if ($user) {
               sms::dispatch($user->phone, $user->code)->onQueue('sms')->onConnection('database')->delay(now()->addSeconds(2));
               return back()->with('mensaje', 'Mensaje enviado de nuevo.');
           } else {
               return back()->with('error', 'Usuario no encontrado.');
           }
       }



       public function verificarUser(Request $request){
        
        try {
            $key = 'registro-' . $request->ip();
            $maxAttempts = 2;
            $decayMinutes = 1;
            $limiter = app(RateLimiter::class);
            
            if ($limiter->tooManyAttempts($key, $maxAttempts)) {
                $seconds = $limiter->availableIn($key);
                throw new ThrottleRequestsException('Demasiados intentos. Por favor, inténtelo de nuevo en ' . $seconds . ' segundos.');
            }
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => env('RECAPTCHA_SECRET'),
                'response' => $request->input('g-recaptcha-response')
            ])->object();
    
            
            if ($response->success == true && $response->score >= 0.7) {
    
                $validacion = Validator::make($request->all(), [
                    'code' => ['required', 'numeric'],
                    
                ], [
                    
                    'code.required' => 'El campo código es obligatorio.',
                    'code.numeric' => 'El campo código debe ser numérico.',
    
                ]);

                $user = User::where('code', $request->code)->first();

                if (!$user) {
                    // Manejar el caso cuando el usuario no se encuentra
                    return redirect('/')
                        ->with('error', 'Código de usuario no válido.')
                        ->withInput();
                }else{
                    $user->status=1;
                    $user->save;
                    if ($user->save()){
                        session()->flash('mensaje', '¡Ya puede Iniciar Session!');
                        dd($user);
                    }
                }

                

                
    
                if ($validacion->fails()) {
                    return redirect('/')
                        ->withErrors($validacion)
                        ->withInput();
                }
           
            } else {
                session()->flash('error', 'Erros de sistema. Por favor, inténtelo de nuevo.');
            }
        } catch (\Exception $e) {
            // Capturar y manejar la excepción, puedes loguearla, enviar notificaciones, etc.
            session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo.');
            
            
        }
    
        return redirect('/');

       }
       
       
       
       public function login(Request $request){
       

           try {
               $key = 'registro-' . $request->ip();
               $maxAttempts = 2;
               $decayMinutes = 1;
               $limiter = app(RateLimiter::class);
               
               if ($limiter->tooManyAttempts($key, $maxAttempts)) {
                   $seconds = $limiter->availableIn($key);
                   throw new ThrottleRequestsException('Demasiados intentos. Por favor, inténtelo de nuevo en ' . $seconds . ' segundos.');
               }
               $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                   'secret' => env('RECAPTCHA_SECRET'),
                   'response' => $request->input('g-recaptcha-response')
               ])->object();
       
               
               if ($response->success == true && $response->score >= 0.7) {
       
                   $validacion = Validator::make($request->all(), [
                       'email' => ['required', 'string', 'email', 'max:35',],
                       'password' => ['required', 'string', 'min:8', 'confirmed'],
                       
                   ], [
                       
                       'email.required' => 'El campo correo electrónico es obligatorio.',
                       'email.string' => 'El campo correo electrónico debe ser una cadena de caracteres.',
                       'email.email' => 'El formato del correo electrónico no es válido.',
                       'email.max' => 'El campo correo electrónico no puede exceder los 35 caracteres.',
                       
                       'password.required' => 'El campo contraseña es obligatorio.',
                       'password.string' => 'El campo contraseña debe ser una cadena de caracteres.',
                       'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
       
                   ]);
       
                   if ($validacion->fails()) {
                       return redirect('/')
                           ->withErrors($validacion)
                           ->withInput();
                   }
              
               } else {
                   session()->flash('error', 'Erros de sistema. Por favor, inténtelo de nuevo.');
               }
           } catch (\Exception $e) {
               // Capturar y manejar la excepción, puedes loguearla, enviar notificaciones, etc.
               session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo.');
               
               
           }
       
           return redirect('/');
       
       }
       
       
       public function vistaCode(Request $request, $idUser)
       {
           // Verifica la validez de la firma
           if (!$request->hasValidSignature()) {
       
               session()->flash('error', 'Erros de sistema. Por favor, inténtelo de nuevo.');
               return redirect('/');
               
           }
       
           // Procesa la solicitud y devuelve la vista
           return view('s-code', ['idUser' => $idUser]);
       }
           
}
