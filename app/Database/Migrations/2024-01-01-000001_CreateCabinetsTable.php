<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCabinetsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'db' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
            ],
            'db_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'exchange' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'cab' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'address' => [
                'type' => 'TEXT',
            ],
            'lat' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
            ],
            'lng' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['exchange', 'cab']);
        $this->forge->addKey('db');

        $this->forge->createTable('cabinets');
    }

    public function down()
    {
        $this->forge->dropTable('cabinets');
    }
}
