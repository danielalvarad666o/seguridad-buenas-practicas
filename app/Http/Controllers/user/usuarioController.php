<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use App\Jobs\sms;
use App\Models\User;
use App\Http\Controllers\servicios\servicioController;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;


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
                   Log::info('Intentaron Atackar la pagina '.$key);
                   throw new ThrottleRequestsException('Demasiados intentos. Por favor, inténtelo de nuevo en ' . $seconds . ' segundos.');
               }
               //validacion recaptcha
               $serviciosController = new servicioController();
               $response = $serviciosController->verificarRecaptcha($request);
               if (Auth::check()) {
                // Si el usuario ya está autenticado, redirigirlo a otra página, por ejemplo, la página de inicio
                return redirect()->route('inicio');
            }
               
               if ($response->success == true && $response->score >= 0.7) {
                $validacion = Validator::make($request->all(), [
                    'name' => ['required', 'string', 'regex:/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/', 'max:50'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'password' => ['required', 'string', 'min:8', 'confirmed'],
                    'phone' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:10', 'max:10', 'unique:users,phone'],

                ], [
                    'name.required' => 'El campo nombre es obligatorio.',
                    'name.string' => 'El campo nombre debe ser con puras letras.',
                    'name.regex' => 'Solo se permiten letras y espacios en el campo nombre.',
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
                    'phone.required'=> 'El campo teléfono es obligatorio.',
                    'phone.string' => 'El campo teléfono debe ser una cadena de caracteres.',
                    'phone.regex' => 'Solo se permiten números  en el campo teléfono.',
                    'phone.min'=>'El campo teléfono debe tener 10 dígitos.',
                    'phone.max'=>'El campo teléfono debe tener 10 dígitos.',
                    'phone.unique' => 'El teléfono ya está registrado.',
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
                   
                   $codigoCifrado = Crypt::encryptString($numero_aleatorio);
                   
                       $user = User::create([
                           'name' => $request->name,
                           'email' => $request->email,
                           'password' => Hash::make($request->password),
                           'phone' => $request->phone,
                           'rol_id'=>$variableResultado,
                           'code' => $codigoCifrado,
                       ]);
                   
                       
                       
                   
                   
                   if ($user->save()){
                    Log::info('Se a creado un usuario : ' . $user);
                  #sms::dispatch($request->phone,$numero_aleatorio)->onQueue('sms')->onConnection('database')->delay(now()->addSeconds(2));
                   


                   if ($user->rol_id==1){
                    session()->flash('mensaje', '¡Usuario registrado correctamente!');
                    
                     
           $url = URL::temporarySignedRoute(
               'code',
               now()->addMinutes(10),
               ['userId' => $user->id]
           );
           sms::dispatch($user->phone, $user->code)->onQueue('sms')->onConnection('database')->delay(now()->addSeconds(2));
           
           return redirect()->to($url);
               
                   
        }else
        {
            
            $user->status=1;
            $user->save;
            if ($user->save()){
                session()->flash('mensaje', '¡Ya puede Iniciar Session!');
                return redirect()->route("iniciarSesion");
                
            }
        }
       
       
                   }else{
                       session()->flash('error', '¡El usuario no se pudo guardar intentelo de nuevo!');
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
        
           try {
               // Utilizar $userId en tu lógica
               $user = User::find($userId);

               $codigoDescifrado = Crypt::decryptString($user->code);
   
               if ($user) {
                   sms::dispatch($user->phone, $user->code)->onQueue('sms')->onConnection('database')->delay(now()->addSeconds(2));
                   Log::info('se a enviado un sms alusuario : ' . $user->id);
                   return back()->with('mensaje', 'Mensaje enviado de nuevo.');
               } else {
                   return back()->with('error', 'Usuario no encontrado.');
               }
           } catch (\Exception $e) {
               // Manejar la excepción
               Log::error('Error en resetSMS: ' . $e->getMessage());
   
               
               Log::error('Trace: ' . $e->getTraceAsString());
   
               
               return back()->with('error', 'Ocurrió un error al enviar el mensaje.');
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
                
                
                
                $user = User::first();
                $codigoDescifrado = Crypt::decryptString($user->code);
                

               
                


                if ($request->code!=$codigoDescifrado) {
                    // Manejar el caso cuando el usuario no se encuentra
                    return redirect()->intended('/')
                        ->with('error', 'Código de usuario no válido.')
                        ->withInput();
                }else{

                    
                    if($user->status==0){
                    $user->status=1;
                    $user->save;
                    if ($user->save()){
                        session()->flash('mensaje', '¡Ya puede Iniciar Session!');
                        Log::info('verifico el code el usuario : ' . $user->id);
                        return redirect()->route("iniciarSesion");
                        
                    }
                }else{
                    Auth::login($user);
                    session()->flash('mensaje', 'Bienvenido');
                    return redirect()->route('inicio');
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
       
       
       
       public function login(Request $request)
       {
           try {
               $key = 'registro-' . $request->ip();
               $maxAttempts = 2;
               $decayMinutes = 1;
               $limiter = app(RateLimiter::class);
       
               if ($limiter->tooManyAttempts($key, $maxAttempts)) {
                   $seconds = $limiter->availableIn($key);
                   throw new TooManyRequestsHttpException($seconds, 'Demasiados intentos. Por favor, inténtelo de nuevo en ' . $seconds . ' segundos.');
               }

               if (Auth::check()) {
                // Si el usuario ya está autenticado, redirigirlo a otra página, por ejemplo, la página de inicio
                return redirect()->route('inicio');
            }
       
               $serviciosController = new servicioController();
               $response = $serviciosController->verificarRecaptcha($request);
       
               if ($response->success && $response->score >= 0.7) {
                   $validacion = Validator::make($request->all(), [
                       'email' => ['required', 'string', 'email', 'max:35'],
                       'password' => ['required', 'string', 'min:8'],
                   ]);
       
                   if ($validacion->fails()) {
                       throw ValidationException::withMessages($validacion->errors()->toArray());
                   }
                   $user = User::where('email',$request->email)->first();
                   
                   if ($user) {
                       
                 
                    if(Hash::check($request->password,$user->password))
                    { 


                       
       
                       if ($user->rol_id == 1) {
                        
                           srand(time());
                           $numero_aleatorio = rand(5000, 6000);
                           $codigoCifrado = Crypt::encryptString($numero_aleatorio);
       
                           $user->code = $codigoCifrado;
                           $user->save;

       
                           if ($user->save()) {
                            
                               sms::dispatch($user->phone, $user->code)->onQueue('sms')->onConnection('database')->delay(now()->addSeconds(2));
       
                               $url = URL::temporarySignedRoute(
                                   'code',
                                   now()->addMinutes(20),
                                   ['userId' => $user->id]
                               );
                               
                              
                               return redirect()->to($url);
                           } else {
                            
                               session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo.');
                               Log::info('el Usuario no pudo entrar : ' . $user->id);
                               return redirect()->route('login');
                           }



                           session()->flash('error', 'Ocurrió un error con las credenciales.');
                           Log::info('el Usuario no pudo entrar con contraseña  : ' . $user->id);
                           return redirect()->route('login');

                        }else{
                            Auth::login($user);
                            
                            
                           Log::info('Usuario normal sesión iniciada correctamente: ' . $user->id);
                           return redirect()->route('inicio');
                        }









                       } else {
                           session()->flash('mensaje', 'Bienvenido');
                           Log::info('Usuario normal sesión iniciada correctamente: ' . $user->id);


                           return redirect()->route('inicio');
                       }
                   } else {
                       // Manejar el caso cuando el usuario no está autenticado

                       session()->flash('error', 'Error en las credenciales');
                       return redirect()->route('login');
                   }
               } else {
                   Log::error('Error en la verificación del reCAPTCHA');
                   session()->flash('error', 'Error en la verificación del reCAPTCHA');
               }
           } catch (\Exception $e) {
               // Capturar y manejar la excepción, puedes loguearla, enviar notificaciones, etc.
               Log::error('Excepción durante el inicio de sesión: ' . $e->getMessage());
               session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo.');
           }
       
           return redirect()->route('login');
       }
       
       
       public function vistaCode(Request $request, $idUser)
       {
           try {
               $user = User::findOrFail($idUser);
       
               // Verifica la validez de la firma
               if (!$request->hasValidSignature()) {
                   session()->flash('error', 'Errores de sistema. Por favor, inténtelo de nuevo.');
       
                   if ($user->status == 0) {
                       return redirect('/');
                   } else {
                       return redirect()->route("iniciarSesion");
                   }
               }
       
               if ($user->status == 0) {
                   // Procesa la solicitud y devuelve la vista
                   return view('s-code', ['idUser' => $idUser]);
               }
       
               return view('s-smsLogin', ['idUser' => $idUser]);
           } catch (\Exception $e) {
               Log::error('Excepción en vistaCode: ' . $e->getMessage());
               session()->flash('error', 'Ocurrió un error inesperado. Por favor, inténtelo de nuevo.');
               return redirect('/');
           }
       }
       

      
       
       public function logout()
       {
           try {
               Auth::guard('web')->logout();
       
               // Revocar tokens Sanctum si estás utilizando Sanctum
               $user = Auth::guard('sanctum')->user();
               
               if ($user) {
                   $user->tokens()->each(function ($token, $key) {
                       $token->delete();
                   });
               }
       
               session()->flash('mensaje', 'Sessión cerrada correctamente');
           } catch (\Exception $e) {
               Log::error('Excepción durante el cierre de sesión: ' . $e->getMessage());
               session()->flash('error', 'Ocurrió un error inesperado al cerrar sessión');
           }
       
           return redirect('/');
       }
       
           
}
