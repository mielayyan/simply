<?php

class Migration_backoffice_oc_language_sync extends CI_Migration
{

    public function up()
    {
        $dbPrefix = $this->db->dbprefix;
        $query = [];

        if($this->db->table_exists("{$dbPrefix}oc_language")) {
            $query[] = "DROP TABLE {$dbPrefix}oc_language";

            $query[] = "CREATE TABLE `{$dbPrefix}oc_language` (
                        `language_id` int(11) NOT NULL,
                        `name` varchar(32) NOT NULL,
                        `code` varchar(5) NOT NULL,
                        `locale` varchar(255) NOT NULL,
                        `image` varchar(64) NOT NULL,
                        `directory` varchar(32) NOT NULL,
                        `sort_order` int(3) NOT NULL DEFAULT '0',
                        `status` tinyint(1) NOT NULL,
                        `lang_code` varchar(10) NOT NULL
                        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

            $query[] = "ALTER TABLE `{$dbPrefix}oc_language` ADD PRIMARY KEY (`language_id`), ADD KEY `name` (`name`);";

            $query[] = "ALTER TABLE `{$dbPrefix}oc_language` MODIFY `language_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";

            $query[] = "INSERT INTO `{$dbPrefix}oc_language` (`name`, `code`, `locale`, `image`, `directory`, `sort_order`, `status`, `lang_code`) VALUES
                        ('English', 'en-gb', 'en-US,en_US.UTF-8,en_US,en-gb,english', 'gb.png', 'english', 1, 1, 'en'),
                        ('Arabic', 'ar', 'ar-ar', '', '', 2, 1, 'ar'),
                        ('Français', 'fr-Fr', 'fr,fr-Fr', '', '', 3, 1, 'fr'),
                        ('Chinese', 'cn', 'cn', '', '', 4, 1, 'ch'),
                        ('Deutsch', 'de-De', 'de-De,german,Deutsch', '', '', 5, 1, 'de'),
                        ('italiano', 'it-It', 'it-It,it,italiano', '', '', 6, 1, 'it'),
                        ('polski', 'pl-Pl', 'pl,Pl,polski', '', '', 7, 1, 'pl'),
                        ('Português', 'pt-Pt', 'pt-Pt,pt,Português', '', '', 8, 1, 'pt'),
                        ('русский', 'ru-Ru', 'ru-Ru,ru,russian,русский', '', '', 9, 1, 'ru'),
                        ('Español', 'es-Es', 'es-Es,es,spanish,Español', '', '', 10, 1, 'es'),
                        ('Türk', 'tr-Tr', 'tr-Tr,tr,turkish,Türk', '', '', 11, 1, 'tr');";
        }

        foreach ($query as $qry) {
            $this->db->query($qry);
        }
    }

    public function down()
    {
        $dbPrefix = $this->db->dbprefix;

        if ($this->db->table_exists("{$dbPrefix}oc_language")) {
            $this->db->query("ALTER TABLE `{$dbPrefix}oc_language` DROP `lang_code`;");
        }
    }
}
