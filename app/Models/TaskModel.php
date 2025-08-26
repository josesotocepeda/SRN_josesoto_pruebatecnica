<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table            = 'tasks';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = ['title','completed'];

    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    protected $validationRules = [
        'title' => 'required|min_length[1]|max_length[255]',
        'completed' => 'in_list[0,1]'
    ];
}
