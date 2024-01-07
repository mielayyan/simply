<?php

class Migration_Password_policy extends CI_Migration
{

    public function up()
    {
        $dbPrefix = $this->db->dbprefix;
        if(!$this->db->table_exists("password_policy")) {
            $query = [];

            $query[] = "CREATE TABLE `{$dbPrefix}password_policy` (
                    `id` int(11) NOT NULL,
                    `enable_policy` tinyint(4) NOT NULL,
                    `lowercase` tinyint(4) NOT NULL COMMENT 'lowercase status',
                    `uppercase` tinyint(4) NOT NULL COMMENT 'upper case status',
                    `number` tinyint(4) NOT NULL COMMENT 'number status',
                    `sp_char` tinyint(4) NOT NULL COMMENT 'sp. character status',
                    `min_length` tinyint(4) NOT NULL
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
                    
            $query[] = "ALTER TABLE `{$dbPrefix}password_policy` ADD PRIMARY KEY (`id`);";

            $query[] = "ALTER TABLE `{$dbPrefix}password_policy` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";

            $query[] = "INSERT INTO `{$dbPrefix}password_policy` (`enable_policy`, `lowercase`, `uppercase`, `number`, `sp_char`, `min_length`) VALUES (0, 1, 1, 1, 1, 8);";

            foreach ($query as $qry) {
                $this->db->query($qry);
            }
        }
    }

    public function down()
    {
        $dbPrefix = $this->db->dbprefix;
        $this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}password_policy`;");
    }
}
