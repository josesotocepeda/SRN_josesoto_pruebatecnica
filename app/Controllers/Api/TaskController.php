<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Models\TaskModel;

class TaskController extends ResourceController
{
    protected $modelName = TaskModel::class;
    protected $format    = 'json';

    public function index()
    {
        $tasks = $this->model->orderBy('id','desc')->findAll();
        return $this->respond($tasks);
    }

    public function show($id = null)
    {
        $task = $this->model->find($id);
        if (!$task) return $this->failNotFound('Task not found');
        return $this->respond($task);
    }

    public function create()
    {
        // $data = $this->request->getPost();
        // $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();

        if (!$this->model->insert($data, false)) {
            return $this->failValidationErrors($this->model->errors());
        }
        $insertId = $this->model->getInsertID();
        $task = $this->model->find($insertId);
        return $this->respondCreated($task);
    }

    public function update($id = null)
    {
        if ($id === null) return $this->failValidationErrors('Missing ID');

        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();
        if (!$this->model->find($id)) return $this->failNotFound('Task not found');

        if (!$this->model->update($id, $data)) {
            return $this->failValidationErrors($this->model->errors());
        }
        $task = $this->model->find($id);
        return $this->respond($task);
    }

    public function delete($id = null)
    {
        if ($id === null) return $this->failValidationErrors('Missing ID');
        if (!$this->model->find($id)) return $this->failNotFound('Task not found');
        $this->model->delete($id);
        return $this->respondDeleted(['id' => $id]);
    }
}
