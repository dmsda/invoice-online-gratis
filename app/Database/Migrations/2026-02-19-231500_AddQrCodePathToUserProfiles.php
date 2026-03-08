<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQrCodePathToUserProfiles extends Migration
{
    public function up()
    {
        $this->forge->addColumn('user_profiles', [
            'qr_code_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'logo_path',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('user_profiles', 'qr_code_path');
    }
}
