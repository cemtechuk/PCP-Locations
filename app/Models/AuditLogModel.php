<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table         = 'audit_log';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['user_id', 'username', 'action', 'entity_type', 'entity_id', 'description', 'created_at'];

    public function record(string $action, string $entityType, ?int $entityId, string $description): void
    {
        $this->insert([
            'user_id'     => session()->get('user_id'),
            'username'    => session()->get('username'),
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'description' => $description,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public function getRecent(int $limit = 20): array
    {
        return $this->orderBy('created_at', 'DESC')->limit($limit)->findAll();
    }
}
