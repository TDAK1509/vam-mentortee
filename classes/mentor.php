<?php
class VamMentor {
  private $table_name;

  function __construct() {
    global $wpdb;
    $this->table_name = $wpdb->prefix . MENTOR_TABLE;
  }
  
  public function createTable() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $this->table_name (
      id mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
      created_at TIMESTAMP,
      name tinytext NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
  }

  public function insertMentor() {
    global $wpdb;

    $wpdb->insert( 
      $this->table_name, 
      array( 
        'name' => 'test'
      ) 
    );
  }
}