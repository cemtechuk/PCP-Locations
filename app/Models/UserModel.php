<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['username', 'email', 'role', 'password', 'created_at', 'updated_at'];

    protected $useTimestamps = true;

    public function findByUsername(string $username): array|null
    {
        return $this->where('username', $username)->first();
    }
}
