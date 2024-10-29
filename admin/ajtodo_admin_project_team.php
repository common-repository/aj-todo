<?php
function ajtodo_admin_project_team(){
	$ajtodo_type = ajtodo_get("ajtodo_type", get_option('ajtodo_set_defaultpage', "todo"));
	$pid = isset($_GET['pid']) ? sanitize_text_field($_GET['pid']) : "";
	$common = new AJTODO_Common();
	$common->loadCommon();
	$common->start("container");

	$prj = new AJTODO_Project();
	$prj->id = $pid;
	$prj->setProject();

    //wp_enqueue_style('ajtodo_main_css', AJTODO_PLUGIN_URL."css/ajtodo.css");
    wp_enqueue_script('ajtodo_project_js', AJTODO_JS_PATH."ajtodo_project.".AJTODO_JSMIN."js", array('jquery'), '1.0', true );
	wp_localize_script('ajtodo_project_js', 'ajax_project_info', array(
        'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajtodo-ajax-nonce'),
		'type' => $ajtodo_type,
		'pid' => $pid
	));
	
    wp_enqueue_script('ajtodo_project_team_js', AJTODO_JS_PATH."ajtodo_project_team.".AJTODO_JSMIN."js", array('jquery'), '1.0', true );
	wp_localize_script('ajtodo_project_team_js', 'ajax_project_team_info', array(
        'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajtodo-ajax-nonce'),
		'type' => $ajtodo_type,
		'pid' => $pid
	));

	echo "<div class='row'>";
	echo "<div class='col-md-3'>";
	echo "<h1>[".$prj->pkey."] ".$prj->title;
	if($ajtodo_type == "doc" && $prj->hasPerm("tp_doc_create")){
		echo "<a href='post-new.php?post_type=ajtododoc&pid=".$pid."' class='btn btn-primary btn-sm' style='margin-left:8px;margin-top: -4px;'>".__("새 문서","ajtodo")."</a>";
	}
	echo "</h1>";
	echo "</div>";
	echo "<div class='col-md-8' style='margin-left: auto'>";
	include(AJTODO_PLUGIN_PATH . "admin/admin.team.project.menu.php");
	echo "</div>";
	echo "</div>";

	echo "<div class='ajtodo_project_set' id='ajtodo_project_set'>";
	switch($ajtodo_type){
		case "edit":
			ajtodo_admin_project_team_add($common, $pid);
			break;
		case "status":
			ajtodo_admin_project_team_status($prj);
			break;
		case "report":
			ajtodo_admin_project_team_report($prj);
			break;
		case "todotype":
			ajtodo_admin_project_team_todotype($prj);
			break;
		case "member":
			ajtodo_admin_project_team_member($prj);
			break;
		case "category":
			ajtodo_admin_project_team_category($prj);
			break;
		case "role":
			ajtodo_admin_project_team_role($prj);
			break;
		case "todo":
			ajtodo_admin_project_team_todo($prj);
			break;
		case "plan":
			ajtodo_admin_project_team_plan($prj);
			break;
		case "doc":
			ajtodo_admin_project_team_doc($prj);
			break;
		case "board":
			ajtodo_admin_project_team_plan($prj, "kanban");
			break;
		case "dashboard":
			ajtodo_admin_project_team_dashboard($prj);
			break;
	}
	echo "</div>";
	$common->last();
}

function ajtodo_admin_project_team_dashboard($prj){
	echo "project dashboard";
}

//function ajtodo_admin_project_team_report($pid){
//	$prj = new AJTODO_Project();
//	$prj->id = $pid;
//	echo "project report";
//}

function ajtodo_admin_project_team_doc($prj){
	require_once(AJTODO_PLUGIN_PATH . "inc/ajtodo_doc_table.php");
	$ajtodo_doc_table = new AJTODO_Doc_Table($prj->id);
	$ajtodo_doc_table->project = $prj;
	$ajtodo_doc_table->prepare_items();
	$ajtodo_doc_table->views();
	$ajtodo_doc_table->display();
}

function ajtodo_admin_project_team_add($common, $pid){
	$prj = new AJTODO_Project();
	$prj->id = $pid;
	echo $prj->createForm();
}
