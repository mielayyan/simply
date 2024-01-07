<?php

class Migration_rank_status_demo extends CI_Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `{$this->db->dbprefix}module_status` ADD `rank_status_demo` VARCHAR(5) NOT NULL DEFAULT 'no' AFTER `rank_status`;");
        if($this->db->where('rank_status', 'yes')->count_all_results('module_status')) {
            $this->db->update('module_status', ['rank_status_demo' => 'yes']);
        }
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `{$this->db->dbprefix}module_status` DROP `rank_status_demo`;");
    }
}
