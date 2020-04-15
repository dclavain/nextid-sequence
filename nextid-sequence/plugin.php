<?php
/*
Plugin Name: Next id sequence
Plugin URI: https://github.com/dclavain/yourls-nextid-sequence
Description: Gets the next_id from a table with an auto_increment identifier.
Version: 1.0
Author: dclavain
Author URI: https://github.com/dclavain
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// Hook custom function into the 'get_next_decimal' filter
yourls_add_filter( 'get_next_decimal', 'nextid_sequence_get_next_decimal' );

// Hook activated/deactivated actions.
yourls_add_action( 'activated_nextid-sequence/plugin.php', 'nextid_sequence_install' );
yourls_add_action( 'deactivated_nextid-sequence/plugin.php', 'nextid_sequence_uninstall' );

/**
 * Implements hook get_next_decimal filter.
 * @return int
 */
function nextid_sequence_get_next_decimal() {
  global $ydb;
  $ydb->query("insert into yourls_id values();");
  $results = $ydb->get_results("SELECT @@identity AS id;");

  return (int)$results[0]->id;
}

/**
 * Implements hook activated action.
 */
function nextid_sequence_install () {
  global $ydb;

  $ydb->query("create table `yourls_id` (
		 `id` int NOT NULL auto_increment,
		 PRIMARY KEY  (`id`));");

  $results = $ydb->get_results("select option_value from yourls_options where option_name ='next_id';");
  $ydb->query("ALTER TABLE yourls_id AUTO_INCREMENT =" . $results[0]->option_value);

}

/**
 * Implements hook deactivated action.
 */
function nextid_sequence_uninstall() {
  global $ydb;
  $ydb->query("drop table yourls_id;");
}