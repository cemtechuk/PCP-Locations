<?php

namespace App\Models;

use CodeIgniter\Model;

class ApiKeyModel extends Model
{
    protected $table         = 'api_keys';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['name', 'api_key', 'active', 'rate_limit', 'created_by', 'last_used_at'];
    protected $useTimestamps  = true;

    public function findByKey(string $key): array|null
    {
        return $this->where('api_key', $key)->where('active', 1)->first();
    }

    public function touchLastUsed(int $id): void
    {
        $this->db->table('api_keys')
            ->where('id', $id)
            ->update(['last_used_at' => date('Y-m-d H:i:s')]);
    }

    public function generateKey(): string
    {
        return bin2hex(random_bytes(32)); // 64-char hex string
    }
}
