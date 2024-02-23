<?php

namespace App\Http\Controllers\servicios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class servicioController extends Controller
{
    //
    public function verificarRecaptcha(Request $request)
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET'),
            'response' => $request->input('g-recaptcha-response'),
        ])->object();

        return $response;
    }
}
