<?php

namespace Tests\App\Controllers\Api;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class TaskControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    /**
     * 1) Probar creación de tarea vía POST /api/tasks
     */
    public function testCreateTask()
    {
        $data = [
            'title' => 'Tarea de prueba',
            'completed' => 0
        ];

        $result = $this->withBody(json_encode($data))
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post('/api/tasks');

        $result->assertStatus(201); // creado
        $result->assertJSONFragment(['title' => 'Tarea de prueba']);
    }

    /**
     * 2) Probar listado de tareas vía GET /api/tasks
     */
    public function testListTasks()
    {
        $result = $this->get('/api/tasks');

        $result->assertStatus(200);
        $this->assertIsArray($result->getJSON(true)); // devuelve array
    }

    /**
     * 3) Probar actualización de tarea vía PUT /api/tasks/{id}
     */
    public function testUpdateTask()
    {
        // Crear primero
        $data = ['title' => 'Temp'];
        $create = $this->withBody(json_encode($data))
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post('/api/tasks');

        $task = $create->getJSON(true);
        $id = $task['id'] ?? null;
        $this->assertNotNull($id, 'No se pudo crear tarea de prueba');

        // Actualizar
        $updateData = ['title' => 'Tarea actualizada', 'completed' => 1];
        $update = $this->withBody(json_encode($updateData))
            ->withHeaders(['Content-Type' => 'application/json'])
            ->put("/api/tasks/{$id}");

        $update->assertStatus(200);
        $update->assertJSONFragment(['title' => 'Tarea actualizada', 'completed' => 1]);
    }
}
