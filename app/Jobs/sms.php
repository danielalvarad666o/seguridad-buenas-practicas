<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class sms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $numero;
    protected $numero_aleatorio;

    /**
     * Create a new job instance.
     */
    public function __construct($numero,$numero_aleatorio)
    {
        //
        $this->numero=$numero;
        $this->numero_aleatorio=$numero_aleatorio;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $responseSMS = Http::post('https://rest.nexmo.com/sms/json', [
            "from" => "Vonage APIs",
            'api_key' => env('VONAGE_API_KEY'),
            'api_secret' => env('VONAGE_API_SECRET'),
            'to' => "52{$this->numero}",
            'text' => "tu numero de verificacion es: {$this->numero_aleatorio}",
        ]);
        
    }
}
