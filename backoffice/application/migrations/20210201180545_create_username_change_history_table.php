
<?php

class Migration_create_username_change_history_table extends CI_Migration
{
	
	public function up()
    {
        $dbPrefix = $this->db->dbprefix;
        $query = [];

        $query[] = "DROP TABLE IF EXISTS `{$dbPrefix}username_change_history`;";

        $query[] = "CREATE TABLE `{$dbPrefix}username_change_history` (
                      `id` INT UNSIGNED NOT NULL , `user_id` INT(11) NOT NULL , `old_username` VARCHAR(20) NOT NULL , `new_username` VARCHAR(20) NOT NULL , `modified_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

        $query[] = "ALTER TABLE `{$dbPrefix}username_change_history` ADD KEY `id` (`id`);";

        $query[] = "ALTER TABLE `{$dbPrefix}username_change_history` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;";


        foreach ($query as $qry) {
            $this->db->query($qry);
        }
    }

    public function down()
    {
        $dbPrefix = $this->db->dbprefix;
        $this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}username_change_history`;");
    }

}