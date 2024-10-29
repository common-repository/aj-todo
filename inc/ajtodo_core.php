<?php
global $table_prefix;
$ajtodoVersion = "1.2.2";
$ajtodoDBVersion = "1.3";

define('AJTODO_ISSUE_STATUS_S', "S");	//작업전
define('AJTODO_ISSUE_STATUS_I', "I");	//작업중
define('AJTODO_ISSUE_STATUS_D', "D");	//작업완료

define('AJTODO_PROJECT_STATUS_S', "S");	//Start
define('AJTODO_PROJECT_STATUS_I', "I");	//Inprogress
define('AJTODO_PROJECT_STATUS_D', "D");	//Done
define('AJTODO_PROJECT_STATUS_P', "P");	//Pending

define('AJTODO_DB_PROJECT', $table_prefix.'ajtodo_project');
define('AJTODO_DB_TODO', $table_prefix.'ajtodo_issue');
define('AJTODO_DB_TODOSTATUS', $table_prefix.'ajtodo_issuestatus');
define('AJTODO_DB_TODOCOMMENT', $table_prefix.'ajtodo_issuecomment');
define('AJTODO_DB_PROJECTUSER', $table_prefix.'ajtodo_projectuser');
define('AJTODO_DB_PROJECTKEYS', $table_prefix.'ajtodo_projectkeys');
define('AJTODO_DB_PLAN', $table_prefix.'ajtodo_plan');
define('AJTODO_DB_LINK', $table_prefix.'ajtodo_link');
if(AJTODO_PLUGIN_IDEV){
	define('AJTODO_JS_PATH', AJTODO_PLUGIN_URL."js_src/");
	define('AJTODO_JSMIN', "");
}else{
	define('AJTODO_JS_PATH', AJTODO_PLUGIN_URL."js/");
	define('AJTODO_JSMIN', "min.");
}

require_once(AJTODO_PLUGIN_PATH . "inc/ajtodo_activation.php");
require_once(AJTODO_PLUGIN_PATH . "inc/ajtodo_admin.php");
require_once(AJTODO_PLUGIN_PATH . "admin/admin.doc.php");
require_once(AJTODO_PLUGIN_PATH . "admin/admin.posttype.php");
require_once(AJTODO_PLUGIN_PATH . "admin/admin.virtual.page.php");
require_once(AJTODO_PLUGIN_PATH . "admin/admin.dashboard.php");
require_once(AJTODO_PLUGIN_PATH . "admin/admin.meta.box.php");
require_once(AJTODO_PLUGIN_PATH . "admin/ajtodo_admin_setting.php");
require_once(AJTODO_PLUGIN_PATH . "admin/ajtodo_admin_todo.php");
require_once(AJTODO_PLUGIN_PATH . "admin/ajtodo_admin_project.php");
require_once(AJTODO_PLUGIN_PATH . "admin/ajtodo_admin_project_private.php");
require_once(AJTODO_PLUGIN_PATH . "admin/ajtodo_admin_project_team.php");
require_once(AJTODO_PLUGIN_PATH . "admin/ajtodo_admin_project_team.member.php");
require_once(AJTODO_PLUGIN_PATH . "admin/ajtodo_admin_project_team.roleperm.php");
require_once(AJTODO_PLUGIN_PATH . "admin/ajtodo_admin_project_team.status.php");
require_once(AJTODO_PLUGIN_PATH . "admin/ajtodo_admin_project_team.category.php");
require_once(AJTODO_PLUGIN_PATH . "admin/ajtodo_admin_project_team.todotype.php");
require_once(AJTODO_PLUGIN_PATH . "admin/ajtodo_admin_project_team.todo.php");
require_once(AJTODO_PLUGIN_PATH . "admin/ajtodo_admin_project_team.plan.php");
require_once(AJTODO_PLUGIN_PATH . "admin/ajtodo_admin_project_team.report.php");

require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_default.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_lan.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_common.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_status.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_role.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_projectrole.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_projectmember.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_projectstatus.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_projectcategory.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_projecttodotype.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_projectplan.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_projectlink.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_todo.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_todolist.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_project_todolist.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_project.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_user.php");
require_once(AJTODO_PLUGIN_PATH . "core/ajtodo_projectlist.php");

require_once(AJTODO_PLUGIN_PATH . "inc/ajtodo_common.php");

if(!isset($_POST)){
	include(AJTODO_PLUGIN_PATH . "inc/ajtodo_js.php");
}
