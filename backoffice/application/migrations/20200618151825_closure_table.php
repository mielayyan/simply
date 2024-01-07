<?php

class Migration_closure_table extends CI_Migration
{

    public function up()
    {
        $dbPrefix = $this->db->dbprefix;
        $query = [];

        $query[] = "ALTER TABLE `{$dbPrefix}ft_individual` ADD `leg_position` INT NOT NULL AFTER `position`;";
        
        $query[] = "UPDATE `{$dbPrefix}ft_individual` SET leg_position =
                    CASE WHEN user_type = 'admin' THEN 0
                    WHEN user_type = 'user' AND position = 'L' THEN 1
                    WHEN user_type = 'user' AND position = 'R' THEN 2
                    WHEN user_type = 'user' AND position = '' THEN 0
                    ELSE CONVERT(position, UNSIGNED)
                    END";

        $query[] = "CREATE TABLE `{$dbPrefix}treepath` (
                    `ancestor` int(10) UNSIGNED NOT NULL,
                    `descendant` int(10) UNSIGNED NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

        $query[] = "ALTER TABLE `{$dbPrefix}treepath`
                    ADD PRIMARY KEY (`ancestor`,`descendant`),
                    ADD KEY `descendant` (`descendant`);";

        $query[] = "ALTER TABLE `{$dbPrefix}treepath`
                    ADD CONSTRAINT `{$dbPrefix}treepath_ibfk_1` FOREIGN KEY (`ancestor`) REFERENCES `{$dbPrefix}ft_individual` (`id`),
                    ADD CONSTRAINT `{$dbPrefix}treepath_ibfk_2` FOREIGN KEY (`descendant`) REFERENCES `{$dbPrefix}ft_individual` (`id`);";

        $query[] = "CREATE TABLE `{$dbPrefix}sponsor_treepath` (
                    `ancestor` int(10) UNSIGNED NOT NULL,
                    `descendant` int(10) UNSIGNED NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

        $query[] = "ALTER TABLE `{$dbPrefix}sponsor_treepath`
                    ADD PRIMARY KEY (`ancestor`,`descendant`),
                    ADD KEY `descendant` (`descendant`);";

        foreach ($query as $qry) {
            $this->db->query($qry);
        }
    }

    public function down()
    {
        $dbPrefix = $this->db->dbprefix;

        $this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}treepath`;");
        $this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}sponsor_treepath`;");
        $this->db->query("ALTER TABLE `{$dbPrefix}ft_individual` DROP `leg_position`;");
    }
}
