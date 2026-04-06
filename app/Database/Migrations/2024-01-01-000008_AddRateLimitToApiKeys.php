<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRateLimitToApiKeys extends Migration
{
    public function up()
    {
        $this->forge->addColumn('api_keys', [
            'rate_limit' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
                'comment'    => 'Max requests per hour. NULL = unlimited.',
                'after'      => 'active',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('api_keys', 'rate_limit');
    }
}
