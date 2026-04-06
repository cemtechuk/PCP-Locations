<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApiLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT',      'constraint' => 11,  'unsigned' => true, 'auto_increment' => true],
            'api_key_id'  => ['type' => 'INT',      'constraint' => 11,  'unsigned' => true, 'null' => true],
            'key_name'    => ['type' => 'VARCHAR',  'constraint' => 100, 'null' => true],
            'endpoint'    => ['type' => 'VARCHAR',  'constraint' => 150],
            'ip_address'  => ['type' => 'VARCHAR',  'constraint' => 45],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('api_key_id');
        $this->forge->addKey('created_at');
        $this->forge->createTable('api_logs');
    }

    public function down()
    {
        $this->forge->dropTable('api_logs');
    }
}
