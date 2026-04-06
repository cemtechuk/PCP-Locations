<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('users')->insert([
            'username'   => 'admin',
            'email'      => 'admin@pcplocations.local',
            'password'   => password_hash('changeme123', PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        echo "Admin user created.\n";
        echo "  Username: admin\n";
        echo "  Password: changeme123\n";
        echo "Change the password after first login.\n";
    }
}
