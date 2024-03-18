<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
   
     protected function validator(array $data)
     {
        dd($data);
         return Validator::make($data, [
             'name' => ['required', 'string', 'max:50'],
             'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
             'password' => ['required', 'string', 'min:8', 'confirmed'],
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
         ]);
     }
     
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        dd($data);
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
