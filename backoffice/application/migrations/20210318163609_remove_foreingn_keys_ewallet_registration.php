
<?php

class Migration_remove_foreingn_keys_ewallet_registration extends CI_Migration {
	public function up() {
    $dbPrefix = $this->db->dbprefix;
    if($key = $this->isForeignKeyExist($this->db->database, $dbPrefix.'ewallet_payment_details', 'pending_id', $dbPrefix.'pending_registration', 'id')) {
      $this->db->query("ALTER TABLE `{$dbPrefix}ewallet_payment_details` DROP FOREIGN KEY `{$key}`;");
      dump($this->db->last_query());
    }

    if($key = $this->isForeignKeyExist($this->db->database, $dbPrefix.'ewallet_history', 'pending_id', $dbPrefix.'pending_registration', 'id')) {
        $this->db->query("ALTER TABLE `{$dbPrefix}ewallet_history` DROP FOREIGN KEY `{$key}`;");
      dump($this->db->last_query());
    }

    if($key = $this->isForeignKeyExist($this->db->database, $dbPrefix.'ewallet_payment_details', 'user_id', $dbPrefix.'ft_individual', 'id')) {
        $this->db->query("ALTER TABLE `{$dbPrefix}ewallet_payment_details` DROP FOREIGN KEY `{$key}`;");
      dump($this->db->last_query());
    }

    if($key = $this->isForeignKeyExist($this->db->database, $dbPrefix.'ewallet_history', 'from_id', $dbPrefix.'ft_individual', 'id')) {
        $this->db->query("ALTER TABLE `{$dbPrefix}ewallet_history` DROP FOREIGN KEY `{$key}`;");
      dump($this->db->last_query());
    }
  }

  public function down() {
        $dbPrefix = $this->db->dbprefix;

      $this->addForeignKeyIndex($dbPrefix.'ewallet_payment_details', 'pending_id', $dbPrefix.'pending_registration', 'id');
      $this->addForeignKeyIndex($dbPrefix.'ewallet_history', 'pending_id', $dbPrefix.'pending_registration', 'id');
      $this->addForeignKeyIndex($dbPrefix.'ewallet_payment_details', 'user_id', $dbPrefix.'ft_individual', 'id');
      $this->addForeignKeyIndex($dbPrefix.'ewallet_history', 'from_id', $dbPrefix.'ft_individual', 'id');
  }

  public function addForeignKeyIndex($table_name, $column_name, $ref_table, $ref_column, $addon = '') {
      $this->db->query("ALTER TABLE {$table_name} ADD CONSTRAINT  FOREIGN KEY({$column_name}) REFERENCES {$ref_table}({$ref_column}) {$addon}");
      dump($this->db->last_query());
  }

  public function isForeignKeyExist($db, $table, $column, $ref_table, $ref_col) {
    $query = $this->db->query("SELECT CONSTRAINT_NAME from INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA = '{$db}' AND TABLE_NAME = '{$table}' AND COLUMN_NAME = '{$column}' AND REFERENCED_TABLE_NAME = '{$ref_table}' AND REFERENCED_COLUMN_NAME = '$ref_col';");
    $fk_name = $query->row('CONSTRAINT_NAME');
    return $fk_name;
  }

}