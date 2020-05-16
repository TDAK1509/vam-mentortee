<?php
class VamMentor {
  private $table_name;

  function __construct() {
    global $wpdb;
    $this->table_name = $wpdb->prefix . MENTOR_TABLE;
  }

  public function init() {
    $this->createTable();
    add_action('admin_menu', [$this, 'addToAdminMenu']);
  }
  
  private function createTable() {
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

  public function addToAdminMenu() {
    $pageTitle = 'Quản lý Mentor';
    $menuTitle = 'Mentor';
    $capability = 'manage_options';
    $menuSlug = 'vam_mentor';
    $callback = [$this, 'getTemplate'];
    $iconUrl = 'dashicons-businessman';
    $position = null;

    add_menu_page($pageTitle, $menuTitle, $capability, $menuSlug, $callback, $iconUrl, $position);
  }

  public function getTemplate() {
    require_once DIR_PLUGIN . 'templates/mentor.php';
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