<?php

/**
 * Plugin Name:       Murmurations Aggregator
 * Plugin URI:        murmurations.network
 * Description:       Collect and display data from the Murmurations network
 * Version:           0.1.0-alpha
 * Author:            A. McKenty / Photosynthesis
 * Author URI:        Photosynthesis.ca
 * License:           Peer Production License
 * License URI:
 * Text Domain:       murmurations
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require plugin_dir_path( __FILE__ ) . 'includes/murmurations-aggregator-wp.class.php';

$config =  array(
  'plugin_name' => 'Murmurations Aggregator',
  'node_name' => 'Murmurations Node',
  'node_name_plural' => 'Murmurations Nodes',
  'node_slug' => 'murmurations_node',
  'feed_storage_path' => plugin_dir_path(__FILE__).'feeds/feeds.json',
  'schema_file' => plugin_dir_path(__FILE__).'schemas/gen_ecovillages_v0.0.1.json',
  'field_map_file' => plugin_dir_path(__FILE__).'schemas/gen_ecovillages_field_map.json',
);


$mawp = new Murmurations_Aggregator_WP($config);

/* Development logging fallbacks */

if(is_callable('murms_flush_log')){
  add_action('wp_footer', 'murms_flush_log');
  add_action('admin_footer', 'murms_flush_log');
}

if(!is_callable('llog')){
  function llog(){
    return false;
  }
}

?>
