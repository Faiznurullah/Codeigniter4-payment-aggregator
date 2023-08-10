<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusColumnToUsers extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE transactionpayment ADD COLUMN status ENUM("unpaid", "paid", "failed", "expired") NOT NULL DEFAULT "unpaid"');
    }

    public function down()
    {
        //
    }
}
