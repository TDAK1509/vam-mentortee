<?php
if (!defined('ABSPATH')) {
  die;
}

require_once "mentor-admin.php";
require_once "mentor-view.php";

class VamMain {
  function __construct() {
    if ($this->isAdminPage() === true) {
      VamMentorAdmin::init();
    }

    if ($this->isMentorListPage() === true) {
      VamMentorView::init();
    }
  }

  private function isAdminPage() {
    return $this->isPageUriContainingWord("/wp-admin");
  }

  private function isMentorListPage() {
    return $this->isPageUriContainingWord("/mentor-list");
  }

  private function isPageUriContainingWord($word) {
    $pageUri = $_SERVER['REQUEST_URI'];

    if (strpos($pageUri, $word) === false) {
      return false;
    }

    return true;
  }

  function activate() {
    flush_rewrite_rules();
  }

  function deactivate() {
    flush_rewrite_rules();
  }

  function uninstall() {

  }
}