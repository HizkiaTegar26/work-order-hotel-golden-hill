<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPreferredLanguageToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'preferred_language' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'default'    => 'id',
                'after'      => 'status_aktif',
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'preferred_language');
    }
}
