<?php

class Migration_Url_perm_subscrption extends CI_Migration {
    public function up() {
        $this->db->set(['perm_dist' => 1, 'perm_admin' => 1, 'perm_emp' => 1])->where('id', 72)->update('infinite_mlm_menu');
    }

    public function down() {
        $this->db->set(['perm_dist' => 0, 'perm_admin' => 0, 'perm_emp' => 0])->where('id', 72)->update('infinite_mlm_menu');
    }
}
