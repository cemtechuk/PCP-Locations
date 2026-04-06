<?php

namespace App\Models;

use CodeIgniter\Model;

class ApiLogModel extends Model
{
    protected $table         = 'api_logs';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['api_key_id', 'key_name', 'endpoint', 'ip_address', 'created_at'];

    public function record(int $keyId, string $keyName, string $endpoint, string $ip): void
    {
        $this->insert([
            'api_key_id' => $keyId,
            'key_name'   => $keyName,
            'endpoint'   => $endpoint,
            'ip_address' => $ip,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getCountPerKey(): array
    {
        return $this->db->table('api_logs')
            ->select('api_key_id, key_name, COUNT(*) as total, MAX(created_at) as last_request')
            ->groupBy('api_key_id, key_name')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getRecentByKey(int $keyId, int $limit = 20): array
    {
        return $this->where('api_key_id', $keyId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function countLastHour(int $keyId): int
    {
        return (int) $this->db
            ->query('SELECT COUNT(*) AS n FROM api_logs WHERE api_key_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)', [$keyId])
            ->getRow()->n;
    }

    public function getTotalToday(): int
    {
        return $this->where('created_at >=', date('Y-m-d 00:00:00'))->countAllResults();
    }

    public function getTotalThisWeek(): int
    {
        return $this->where('created_at >=', date('Y-m-d 00:00:00', strtotime('-7 days')))->countAllResults();
    }
}
