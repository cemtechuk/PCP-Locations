<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ExpandRoleEnum extends Migration
{
    public function up()
    {
        $this->db->query(
            "ALTER TABLE users MODIFY COLUMN role ENUM('guest','viewer','user','editor','admin') NOT NULL DEFAULT 'user'"
        );
    }

    public function down()
    {
        $this->db->query(
            "ALTER TABLE users MODIFY COLUMN role ENUM('user','editor','admin') NOT NULL DEFAULT 'user'"
        );
    }
}
