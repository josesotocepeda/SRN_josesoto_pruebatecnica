<?php

namespace App\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class ResourceControllerTest extends CIUnitTestCase
{
    use ControllerTestTrait;
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $namespace = 'App';

    /**
     * Test 1: Create inválido → 422 + errors.title
     */
    public function testCreateInvalidReturns422WithTitleError()
    {
        // Datos inválidos (sin título requerido)
        $invalidData = [
            'title' => '', 
        ];

        $result = $this->withHeaders([
            'Content-Type' => 'application/json'
        ])->post('/api/tasks', $invalidData);

        // Verificar que retorna status 422 (Unprocessable Entity)
        $result->assertStatus(422);

        // Verificar que contiene error específico del campo title
        $response = $result->getJSON(true);
        
        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('title', $response['errors']);
        $this->assertNotEmpty($response['errors']['title']);
    }

    /**
     * Test 2: Show inexistente → 404
     */
    public function testShowNonExistentResourceReturns404()
    {
        // ID que no existe en la base de datos
        $nonExistentId = 99999;

        $result = $this->withHeaders([
            'Content-Type' => 'application/json'
        ])->get("/api/tasks/{$nonExistentId}");

        // Verificar que retorna status 404 (Not Found)
        $result->assertStatus(404);

        // Opcionalmente verificar el mensaje de error
        $response = $result->getJSON(true);
        $this->assertArrayHasKey('message', $response);
        $this->assertStringContainsString('not found', strtolower($response['message']));
    }

    /**
     * Test 3: Update inválido → 422
     */
    public function testUpdateInvalidReturns422()
    {
        // Primero crear un recurso válido para actualizar
        $validData = [
            'title' => 'Título Original'
        ];

        $createResult = $this->withHeaders([
            'Content-Type' => 'application/json'
        ])->post('/api/tasks', $validData);

        $createResult->assertStatus(201);
        $createdResource = $createResult->getJSON(true);
        $resourceId = $createdResource['data']['id'];

        // Intentar actualizar con datos inválidos
        $invalidUpdateData = [
            'title' => '' // Título vacío (inválido)
        ];

        $result = $this->withHeaders([
            'Content-Type' => 'application/json'
        ])->put("/api/tasks/{$resourceId}", $invalidUpdateData);

        // Verificar que retorna status 422
        $result->assertStatus(422);

        // Verificar que contiene errores de validación
        $response = $result->getJSON(true);
        $this->assertArrayHasKey('errors', $response);
    }

    /**
     * Test 4: Delete exitoso → 204 y no existe luego
     */
    public function testDeleteSuccessfulReturns204AndResourceNotExists()
    {
        // Primero crear un recurso para eliminar
        $validData = [
            'title' => 'Título a Eliminar'
        ];

        $createResult = $this->withHeaders([
            'Content-Type' => 'application/json'
        ])->post('/api/tasks', $validData);

        $createResult->assertStatus(201);
        $createdResource = $createResult->getJSON(true);
        $resourceId = $createdResource['data']['id'];

        // Eliminar el recurso
        $deleteResult = $this->withHeaders([
            'Content-Type' => 'application/json'
        ])->delete("/api/tasks/{$resourceId}");

        // Verificar que retorna status 204 (No Content)
        $deleteResult->assertStatus(204);

        // Verificar que el recurso ya no existe
        $getResult = $this->withHeaders([
            'Content-Type' => 'application/json'
        ])->get("/api/tasks/{$resourceId}");

        $getResult->assertStatus(404);

        // También verificar en la base de datos directamente
        $this->dontSeeInDatabase('resources', ['id' => $resourceId]);
    }

    /**
     * Test adicional: Verificar que create válido funciona correctamente
     */
    public function testCreateValidReturns201()
    {
        $validData = [
            'title' => 'Título Válido',
            'description' => 'Descripción válida'
        ];

        $result = $this->withHeaders([
            'Content-Type' => 'application/json'
        ])->post('/api/tasks', $validData);

        $result->assertStatus(201);
        
        $response = $result->getJSON(true);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($validData['title'], $response['data']['title']);
        
        // Verificar que se guardó en la base de datos
        $this->seeInDatabase('resources', ['title' => $validData['title']]);
    }

    /**
     * Test adicional: Verificar que show existente funciona correctamente
     */
    public function testShowExistentResourceReturns200()
    {
        // Crear un recurso primero
        $validData = [
            'title' => 'Título de Prueba',
            'description' => 'Descripción de prueba'
        ];

        $createResult = $this->withHeaders([
            'Content-Type' => 'application/json'
        ])->post('/api/tasks', $validData);

        $createdResource = $createResult->getJSON(true);
        $resourceId = $createdResource['data']['id'];

        // Obtener el recurso
        $result = $this->withHeaders([
            'Content-Type' => 'application/json'
        ])->get("/api/tasks/{$resourceId}");

        $result->assertStatus(200);
        
        $response = $result->getJSON(true);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals($validData['title'], $response['data']['title']);
    }

    /**
     * Test adicional: Verificar que update válido funciona correctamente
     */
    public function testUpdateValidReturns200()
    {
        // Crear un recurso primero
        $validData = [
            'title' => 'Título Original',
            'description' => 'Descripción original'
        ];

        $createResult = $this->withHeaders([
            'Content-Type' => 'application/json'
        ])->post('/api/tasks', $validData);

        $createdResource = $createResult->getJSON(true);
        $resourceId = $createdResource['data']['id'];

        // Actualizar con datos válidos
        $updateData = [
            'title' => 'Título Actualizado',
            'description' => 'Descripción actualizada'
        ];

        $result = $this->withHeaders([
            'Content-Type' => 'application/json'
        ])->put("/api/tasks/{$resourceId}", $updateData);

        $result->assertStatus(200);
        
        $response = $result->getJSON(true);
        $this->assertEquals($updateData['title'], $response['data']['title']);
        
        // Verificar en la base de datos
        $this->seeInDatabase('resources', [
            'id' => $resourceId,
            'title' => $updateData['title']
        ]);
    }
}