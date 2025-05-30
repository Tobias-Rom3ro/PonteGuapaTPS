<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EnvReadingTest extends TestCase
{
    /**
     * Test that a specific .env variable can be read.
     *
     * @return void
     */
    public function test_env_variable_is_read_correctly()
    {
        // Opcional: Asegúrate de que no haya caché de configuración para esta prueba
        // Esto es útil si sospechas que la caché está causando problemas
        // Aunque para tests, Laravel generalmente carga un entorno limpio.
        // \Artisan::call('config:clear'); // Cuidado: esto afecta el entorno real si no estás en un entorno de test aislado

        // Intenta leer la variable directamente del .env
        $testEnvReading = env('TEST_ENV_READING');
        $anotherTestVar = env('ANOTHER_TEST_VAR');

        // Afirma que la variable tiene el valor esperado
        $this->assertEquals('true', $testEnvReading); // 'true' es un string porque env() devuelve strings
        $this->assertEquals('MySecretValue', $anotherTestVar);

        // También puedes verificar variables de configuración que se basan en .env
        // Por ejemplo, si tienes en config/app.php: 'name' => env('APP_NAME', 'Laravel')
        $appName = config('app.name');
        $this->assertIsString($appName); // Debería ser un string
        $this->assertNotEmpty($appName); // No debería estar vacío
        $this->assertNotEquals('Laravel', $appName, "APP_NAME should be read from .env, not default 'Laravel'");
        // Podrías poner: $this->assertEquals(env('APP_NAME'), $appName);

        // Si llegamos aquí, el .env se está leyendo correctamente.
        $this->assertTrue(true, 'The .env file is being read.');
    }

    /**
     * Test to ensure an expected .env variable is not null (e.g., if it's missing or not read).
     *
     * @return void
     */
    public function test_important_env_variable_is_not_null()
    {
        // Prueba para una variable que debería estar siempre presente, como DB_DATABASE
        $dbDatabase = env('DB_DATABASE');
        $this->assertNotNull($dbDatabase, "DB_DATABASE should not be null, indicating .env is not fully loaded or variable is missing.");
        $this->assertNotEmpty($dbDatabase, "DB_DATABASE should not be empty.");
    }
}
