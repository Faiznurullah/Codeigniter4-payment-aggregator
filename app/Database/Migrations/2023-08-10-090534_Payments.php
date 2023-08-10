<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Payments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'external_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'amount' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'fees' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'mobile_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ["unpaid", "paid", "failed", "expired"],
                'default' => 'unpaid',
            ],
        ]);

        // Define the primary key
        $this->forge->addPrimaryKey('id');

        // Create the table
        $this->forge->createTable('transactionPayment');
    }

    public function down()
    {
        $this->forge->dropTable('transactionPayment');
    }
}
