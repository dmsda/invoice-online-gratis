<?php
namespace App\Controllers;
class DebugDB extends BaseController {
    public function get_fields() {
        $db = \Config\Database::connect();
        $fields = $db->getFieldData('subscriptions');
        echo "SUBSCRIPTIONS: ";
        foreach($fields as $f) {
            echo $f->name . " | ";
        }
    }
}
