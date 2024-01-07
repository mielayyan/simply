<?php

class Migration_adv_settings_subscr_report_menu extends CI_Migration
{

    public function up()
    {
        $dbPrefix = $this->db->dbprefix;
        $query = [];

        $query[] = "INSERT INTO `{$dbPrefix}infinite_urls` (`id`, `link`, `status`, `target`, `sub_menu_ref_id`) VALUES
                    (292, 'configuration/profile_setting', 'yes', 'none', 0),
                    (291, 'select_report/subscription_report', 'yes', 'none', 0);";

        $query[] = "INSERT INTO `{$dbPrefix}infinite_mlm_sub_menu` (`sub_id`, `sub_link_ref_id`, `icon`, `sub_status`, `sub_refid`, `perm_admin`, `perm_dist`, `perm_emp`, `sub_order_id`) VALUES
                    (223, 292, 'clip-home-2', 'yes', 10, 1, 0, 0, 1),
                    (222, 291, 'clip-home-2', 'yes', 16, 1, 0, 1, 17);";

        foreach ($query as $qry) {
            $this->db->query($qry);
        }
    }

    public function down()
    {
        $dbPrefix = $this->db->dbprefix;

        $this->db->query("DELETE FROM `{$dbPrefix}infinite_mlm_sub_menu` WHERE `{$dbPrefix}infinite_mlm_sub_menu`.`sub_id` = 223");

        $this->db->query("DELETE FROM `{$dbPrefix}infinite_mlm_sub_menu` WHERE `{$dbPrefix}infinite_mlm_sub_menu`.`sub_id` = 222");

        $this->db->query("DELETE FROM `{$dbPrefix}infinite_urls` WHERE `{$dbPrefix}infinite_urls`.`id` = 292");

        $this->db->query("DELETE FROM `{$dbPrefix}infinite_urls` WHERE `{$dbPrefix}infinite_urls`.`id` = 291");

    }

}
