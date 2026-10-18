<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationQueueTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_queue' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'recipient_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'recipient_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 25,
                'null'       => true,
            ],
            'recipient_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'notification_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'general',
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'channel' => [
                'type'       => 'ENUM',
                'constraint' => ['whatsapp', 'email', 'all'],
                'default'    => 'whatsapp',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processing', 'sent', 'failed'],
                'default'    => 'pending',
            ],
            'attempts' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
            ],
            'max_attempts' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 3,
            ],
            'failed_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'dispatched_channel' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'processed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_queue', true);
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        $this->forge->createTable('notification_queue', true);
    }

    public function down()
    {
        $this->forge->dropTable('notification_queue', true);
    }
}
