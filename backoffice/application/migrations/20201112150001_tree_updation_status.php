<?php

class Migration_tree_updation_status extends CI_Migration {

    public function up()
    {
        $dbPrefix = $this->db->dbprefix;
        $query = [];

        $query[] = "ALTER TABLE `{$dbPrefix}module_status` ADD `tree_updation` VARCHAR(3) NOT NULL DEFAULT 'no';";

        foreach ($query as $qry) {
            $this->db->query($qry);
        }
    }

    public function down()
    {
        $dbPrefix = $this->db->dbprefix;
        $this->db->query("ALTER TABLE `{$dbPrefix}module_status` DROP `tree_updation`;");
    }

}