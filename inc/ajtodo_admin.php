<?php
function ajtodo_admin_menu(){
    add_menu_page(__('AJ TODO', 'ajtodo' ), __('AJ TODO', 'ajtodo' ), 'manage_options', 'ajtodo-plugin-setting', 'ajtodo_rlist', 'dashicons-yes-alt', 30.2);
    add_submenu_page('ajtodo-plugin-setting', __('프로젝트', 'ajtodo' ), __('프로젝트', 'ajtodo' ), 'read', 'ajtodo_admin_project', 'ajtodo_admin_project');
    //add_submenu_page('ajtodo-plugin-setting', __('할일', 'ajtodo' ), __('할일', 'ajtodo' ), 'read', 'ajtodo_admin_todo', 'ajtodo_admin_todo');
	if(is_admin()){
		add_submenu_page('ajtodo-plugin-setting', __('설정', 'ajtodo' ), __('설정', 'ajtodo' ), 'manage_options', 'ajtodo_admin_setting', 'ajtodo_admin_setting');
	}
    remove_submenu_page('ajtodo-plugin-setting', 'ajtodo-plugin-setting');
}