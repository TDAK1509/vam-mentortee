<?php
class VamMentor {
  private $table_name;
  private $menuSlug = 'vam_mentor_add';

  function __construct() {
    global $wpdb;
    $this->table_name = $wpdb->prefix . MENTOR_TABLE;
  }

  public function init() {
    // $this->createTable();
    // add_action('admin_menu', [$this, 'addToAdminMenu']);
    // add_action('admin_menu', [$this, 'addSubMenuToAdminMenu']);
    add_action('init', [$this, 'addUserRole']);
  }

  public function addUserRole() {
    $capabilities = [
      'read' => true
    ];
    remove_role('mentor');
    add_role('mentor', 'Mentor', $capabilities);
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
    $pageTitle = 'Manage mentors';
    $menuTitle = 'Mentor';
    $capability = 'manage_options';
    $menuSlug = $this->menuSlug;
    $callback = '';
    $iconUrl = 'dashicons-businessman';
    $position = null;

    add_menu_page($pageTitle, $menuTitle, $capability, $menuSlug, $callback, $iconUrl, $position);
  }

  public function addSubMenuToAdminMenu() {
    $parentSlug = $this->menuSlug;
    $capability = 'manage_options';

    add_submenu_page($parentSlug, 'Add new mentor', 'Add mentor', $capability, $parentSlug, [$this, 'getAddMentorTemplate'], null);
    add_submenu_page($parentSlug, 'Update mentor', 'Update mentor', $capability, 'vam_mentor_update', [$this, 'getUpdateMentorTemplate'], null);
  }

  public function getAddMentorTemplate() {
    require_once DIR_PLUGIN . 'templates/mentor_add.php';
  }

  public function getUpdateMentorTemplate() {
    require_once DIR_PLUGIN . 'templates/mentor_update.php';
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