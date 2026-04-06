<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApiKeysTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'api_key'     => ['type' => 'VARCHAR', 'constraint' => 64, 'unique' => true],
            'active'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_by'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'last_used_at'=> ['type' => 'DATETIME', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('api_keys');
    }

    public function down(): void
    {
        $this->forge->dropTable('api_keys');
    }
}
