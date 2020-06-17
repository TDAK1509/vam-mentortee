<?php
require_once "mentor-admin.php";
require_once "mentor-view.php";

class VamMain {
  function __construct() {
    $mentorAdmin = new VamMentorAdmin();
    $mentorAdmin->init();

    VamMentorView::init();
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