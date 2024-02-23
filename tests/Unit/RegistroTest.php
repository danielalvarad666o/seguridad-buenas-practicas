<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class RegistroTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function puede_crear_un_usuario_exitosamente()
    {
        // Simula una solicitud HTTP de recaptcha exitosa
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true, 'score' => 0.8]),
        ]);

        

        // Datos simulados para la solicitud POST
        $userData = [
            'name' => 'Nombre de Prueba',
            'email' => 'correo@prueba.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '1234567890',
            'g-recaptcha-response' => 'respuesta-de-recaptcha',
        ];

        // Ejecuta la solicitud POST para crear un usuario
        $response = $this->post('/registroUsuario', $userData);
        

        // Verifica que la respuesta sea exitosa y redireccione a donde esperas
        $response->assertStatus(302); // 302 es el código de redirección
        $response->assertRedirect('/'); // Ajusta la ruta según tus necesidades

        // Verifica que los datos esperados estén en la base de datos
        $this->assertDatabaseHas('users', [
            'name' => 'Nombre de Prueba',
            'email' => 'correo@prueba.com',
            'phone' => '1234567890',
        ]);

        // Verifica otros aspectos según lo que esperas de tu aplicación
    }
}

