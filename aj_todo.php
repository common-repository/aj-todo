<?php
/**
 * @package AJ Todo
 */
/*
Plugin Name: AJ Todo
Description: Powerfull Project Management Plugin
Version: 1.3.0
Author: AJ Bang
Author URI: https://2p1d.com
License: GPLv2 or later
*/
date_default_timezone_set(get_option('timezone_string'));
define('AJTODO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('AJTODO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AJTODO_PLUGIN_FILE', __FILE__);
define('AJTODO_PLUGIN_IDEV', false);

require_once(plugin_dir_path(__FILE__) . "inc/ajtodo_core.php");

function ajtodo_load_plugin() {
    load_plugin_textdomain( 'ajtodo', FALSE, dirname(plugin_basename( __FILE__ )) . '/languages/' );
}
add_action('plugins_loaded', 'ajtodo_load_plugin');
