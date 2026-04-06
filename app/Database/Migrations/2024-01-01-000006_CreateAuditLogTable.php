<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuditLogTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT',     'constraint' => 11,  'unsigned' => true, 'null' => true],
            'username'    => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],
            'action'      => ['type' => 'VARCHAR', 'constraint' => 30],   // created / updated / deleted
            'entity_type' => ['type' => 'VARCHAR', 'constraint' => 30],   // cabinet / exchange
            'entity_id'   => ['type' => 'INT',     'constraint' => 11, 'unsigned' => true, 'null' => true],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('created_at');
        $this->forge->createTable('audit_log');
    }

    public function down()
    {
        $this->forge->dropTable('audit_log');
    }
}
