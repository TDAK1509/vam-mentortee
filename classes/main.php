<?php
require_once "mentor.php";

class VamMain {
  function __construct() {
    $mentor = new VamMentor();
    $mentor->createTable();
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