<?php

class Migration_app_versioning_tracking extends CI_Migration
{

    public function up()
    {
        $dbPrefix = $this->db->dbprefix;
        $query = [];
        $query[] = "DROP TABLE IF EXISTS `{$dbPrefix}app_config`;";

        $query[] = "CREATE TABLE `{$dbPrefix}app_config` (
                        `id` int(11) NOT NULL,
                        `android_version` float NOT NULL DEFAULT '0',
                        `ios_version` float NOT NULL DEFAULT '0'
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

        $query[] = "INSERT INTO `{$dbPrefix}app_config` (`id`, `android_version`, `ios_version`) VALUES (1, 0, 0);";

        $query[] = "ALTER TABLE `{$dbPrefix}app_config` ADD PRIMARY KEY (`id`);";

        $query[] = "ALTER TABLE `{$dbPrefix}app_config` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;";

        foreach ($query as $qry) {
            $this->db->query($qry);
        }
    }

    public function down()
    {
        $dbPrefix = $this->db->dbprefix;
        $this->db->query("DROP TABLE IF EXISTS `{$dbPrefix}app_config`;");
    }

}
