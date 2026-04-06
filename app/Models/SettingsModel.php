<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table         = 'settings';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['key', 'value'];
    protected $useTimestamps  = true;

    public function getAllKeyed(): array
    {
        $rows = $this->findAll();
        $out  = [];
        foreach ($rows as $row) {
            $out[$row['key']] = $row['value'];
        }
        return $out;
    }

    public function set(string $key, ?string $value): void
    {
        $existing = $this->where('key', $key)->first();

        if ($existing) {
            $this->update($existing['id'], ['value' => $value]);
        } else {
            $this->insert(['key' => $key, 'value' => $value]);
        }
    }
}
