<?php
class VamMentor {
  function createTable() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . MENTOR_TABLE;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
      id mediumint(9) UNSIGNED NOT NULL AUTO_INCREMENT,
      created_at TIMESTAMP,
      name tinytext NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
  }
}