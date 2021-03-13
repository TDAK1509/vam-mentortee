<?php
/**
 * Plugin Name: Vam Mentortee
 * Plugin URI: 
 * Description: Managing plugin for mentor and mentee
 * Version: 1.0
 * Author: TDAK
 * Author URI: http://tdak.me
 */

if (!defined('ABSPATH')) {
  die;
}

require_once "config.php";
require_once "classes/main.php";

// Init plugin
$main = new VamMain();

register_activation_hook(__FILE__, [$main, 'activate']);
