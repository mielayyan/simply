<?php

class Migration_react_menu extends CI_Migration
{
  
  public function up()
    {
        $dbPrefix = $this->db->dbprefix;
        $query = [];

        $query[] = "DROP TABLE IF EXISTS `{$dbPrefix}react_menu`;";

        $query[] = "CREATE TABLE `{$dbPrefix}react_menu` (
                      `id` int(10) UNSIGNED NOT NULL,
                      `title` varchar(30) NOT NULL DEFAULT '',
                      `icon` varchar(30) NOT NULL DEFAULT 'fa fa-circle',
                      `perm_user` tinyint(4) NOT NULL DEFAULT '0',
                      `perm_app` tinyint(4) NOT NULL DEFAULT '0',
                      `show_order` int(11) NOT NULL DEFAULT '100',
                      `status` tinyint(4) NOT NULL DEFAULT '0'
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

        $query[] = "ALTER TABLE `{$dbPrefix}react_menu` ADD KEY `id` (`id`);";

        $query[] = "ALTER TABLE `{$dbPrefix}react_menu` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;";
        
        $query[] = "ALTER TABLE `{$dbPrefix}react_menu` ADD UNIQUE(`id`);";

        $query[] = "INSERT INTO `{$dbPrefix}react_menu` (`id`, `title`, `icon`, `perm_user`, `perm_app`, `show_order`, `status`) VALUES
                      (1, 'network', 'fa fa-sitemap', 1, 1, 3, 1),
                      (2, 'register', 'fa fa-user-plus', 1, 1, 6, 1),
                      (3, 'ewallet', 'fa fa-briefcase', 1, 1, 9, 1),
                      (4, 'payout', 'fa fa-money', 1, 1, 12, 1),
                      (5, 'profile', 'fa fa-address-book-o', 1, 1, 15, 1),
                      (6, 'package', 'fa fa-circle', 1, 1, 18, 1),
                      (7, 'epin', 'fa fa-bookmark-o', 1, 1, 21, 1),
                      (8, 'shopping', 'fa fa-shopping-bag', 1, 1, 24, 1),
                      (9, 'reports', 'fa fa-bar-chart', 1, 1, 27, 1),
                      (10, 'mailbox', 'fa fa-envelope', 1, 1, 30, 1),
                      (11, 'tools', 'fa fa-wrench', 1, 1, 33, 1),
                      (12, 'support', 'fa fa-ticket', 1, 1, 36, 1),
                      (13, 'crm', 'fa fa-users', 1, 1, 36, 1);";


        foreach ($query as $qry) {
            $this->db->query($qry);
        }
    }

    public function down()
    {
        $dbPrefix = $this->db->dbprefix;
        $this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}react_menu`;");
    }

}