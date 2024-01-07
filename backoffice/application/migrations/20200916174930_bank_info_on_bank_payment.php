<?php

class Migration_bank_info_on_bank_payment extends CI_Migration
{
    public function up()
    {
        $dbPrefix = $this->db->dbprefix;
        $query = [];
        if ($this->db->table_exists("bank_transfer_settings")) {
            $this->db->query("DROP TABLE `{$dbPrefix}bank_transfer_settings`");
        }

        $query[] = "CREATE TABLE `{$dbPrefix}bank_transfer_settings` (
                        `id` int(11) NOT NULL,
                        `account_info` text NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

        $query[] = "ALTER TABLE `{$dbPrefix}bank_transfer_settings` ADD PRIMARY KEY (`id`);";

        $query[] = "ALTER TABLE `{$dbPrefix}bank_transfer_settings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;";

        $query[] = "INSERT INTO `{$dbPrefix}bank_transfer_settings` (`account_info`) VALUES ('Account Details');";

        foreach ($query as $qry) {
            $this->db->query($qry);
        }
    }

    public function down()
    {
        $dbPrefix = $this->db->dbprefix;
        if ($this->db->table_exists("bank_transfer_settings")) {
            $this->db->query("DROP TABLE `{$dbPrefix}bank_transfer_settings`");
        }
    }
}
