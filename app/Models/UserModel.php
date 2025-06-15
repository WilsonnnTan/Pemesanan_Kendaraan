<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['name', 'username', 'password', 'role', 'level', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[3]',
        'username' => 'required|min_length[3]|is_unique[users.username,id,{id}]',
        'password' => 'required|min_length[6]',
        'role' => 'required|in_list[admin,approver,user]',
        'level' => 'permit_empty|integer|greater_than[0]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama harus diisi',
            'min_length' => 'Nama minimal 3 karakter'
        ],
        'username' => [
            'required' => 'Username harus diisi',
            'min_length' => 'Username minimal 3 karakter',
            'is_unique' => 'Username sudah digunakan'
        ],
        'password' => [
            'required' => 'Password harus diisi',
            'min_length' => 'Password minimal 6 karakter'
        ],
        'role' => [
            'required' => 'Role harus diisi',
            'in_list' => 'Role tidak valid'
        ],
        'level' => [
            'integer' => 'Level harus berupa angka',
            'greater_than' => 'Level harus lebih dari 0'
        ]
    ];

    public function findByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    public function getApprovers()
    {
        return $this->where('role', 'approver')
                   ->orderBy('level', 'ASC')
                   ->findAll();
    }
} 