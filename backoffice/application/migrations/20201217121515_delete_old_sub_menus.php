<?php
class Migration_Delete_old_sub_menus extends CI_Migration
{

    public function up() {        
        $dbPrefix = $this->db->dbprefix;
        // Epin
        $this->db->delete('infinite_mlm_sub_menu', ['sub_refid' => 13]);
        
        // Ewallet
        $this->db->delete('infinite_mlm_sub_menu', ['sub_refid' => 14]);
        
        // Payout
        $this->db->delete('infinite_mlm_sub_menu', ['sub_refid' => 9]);
    }

    public function down() {
        $dbPrefix = $this->db->dbprefix;
        // Epin
        $this->db->query("INSERT INTO `{$dbPrefix}infinite_mlm_sub_menu` (`sub_id`, `sub_link_ref_id`, `icon`, `sub_status`, `sub_refid`, `perm_admin`, `perm_dist`, `perm_emp`, `sub_order_id`) VALUES (17, '27', '', 'yes', '13', '1', '0', '1', '1'), (18, '28', '', 'yes', '13', '0', '1', '0', '1'), (19, '29', '', 'yes', '13', '0', '1', '0', '2'), (20, '30', '', 'yes', '13', '1', '0', '1', '2'), (27, '32', '', 'yes', '13', '1', '1', '1', '4'), (110, '193', '', 'yes', '13', '1', '1', '1', '3'), (200, '13', '', 'yes', '13', '1', '0', '1', '5')");
        
        // Ewallet
        $this->db->query("INSERT INTO `{$dbPrefix}infinite_mlm_sub_menu` (`sub_id`, `sub_link_ref_id`, `icon`, `sub_status`, `sub_refid`, `perm_admin`, `perm_dist`, `perm_emp`, `sub_order_id`) VALUES (24, '33', '', 'yes', '14', '1', '0', '1', '5'), (25, '34', '', 'yes', '14', '1', '1', '1', '6'), (26, '36', '', 'yes', '14', '1', '1', '1', '7'), (28, '35', '', 'yes', '14', '1', '1', '1', '2'), (78, '128', '', 'yes', '14', '1', '0', '1', '1'), (161, '242', '', 'no', '14', '0', '0', '0', '8'), (199, '277', '', 'yes', '14', '1', '0', '1', '3'), (204, '282', '', 'yes', '14', '1', '0', '1', '4')");

        // Payout
        $this->db->query("INSERT INTO `{$dbPrefix}infinite_mlm_sub_menu` (`sub_id`, `sub_link_ref_id`, `icon`, `sub_status`, `sub_refid`, `perm_admin`, `perm_dist`, `perm_emp`, `sub_order_id`) VALUES (113, '39', '', 'yes', '9', '1', '0', '1', '1'), (152, '227', '', 'yes', '9', '1', '1', '1', '2'), (213, '40', '', 'yes', '9', '0', '1', '0', '1')");
	}
}
