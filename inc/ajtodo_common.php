<?php
require_once(AJTODO_PLUGIN_PATH.'functions.php');
register_activation_hook(AJTODO_PLUGIN_FILE, 'ajtodo_activation');
register_uninstall_hook(AJTODO_PLUGIN_FILE, 'ajtodo_uninstall');
add_action('admin_menu', 'ajtodo_admin_menu');
add_shortcode('ajtodo', 'ajtodo_todolist');
add_shortcode('ajtodoplan', 'ajtodo_plan_info');
