<?php
function ajtodo_admin_project_team_todotype($prj){
	include(AJTODO_PLUGIN_PATH . "inc/ajtodo_js.php");
	$act = isset($_GET["act"]) ? sanitize_text_field($_GET["act"]) : "list";
	switch($act){
		case "list":
			ajtodo_admin_project_team_todotype_list($prj);
			break;
		case "addtodotype":
			ajtodo_admin_project_team_todotype_addtodotype($prj);
			break;
		case "del":
			ajtodo_admin_project_team_todotype_delconfirm($prj);
			break;
	}
}
function ajtodo_admin_project_team_todotype_list($prj){
	$pmem = new AJTODO_ProjectTodoType();
	$pmem->project = $prj;
	echo $pmem->todotypeListView();
}

function ajtodo_admin_project_team_todotype_delconfirm($prj){
	$todotypekey = isset($_GET["todotypekey"]) ? sanitize_text_field($_GET["todotypekey"]) : "";
	$dotype = ajtodo_post("dotype", "");
	$pmem = new AJTODO_ProjectTodoType();
	$pmem->project = $prj;
	if($dotype == "del"){
		$pmem->delTodoType($todotypekey);
		ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=todotype&pid=".$prj->id);
	}else{
		echo $pmem->delConfirmView($todotypekey);
	}
}

function ajtodo_admin_project_team_todotype_addtodotype($prj){
	$todotypekey = isset($_GET["todotypekey"]) ? sanitize_text_field($_GET["todotypekey"]) : "";
	$ptodotype = new AJTODO_ProjectTodoType();
	$ptodotype->project = $prj;
	if(isset($_POST["dotype"])){
		$ajtodo_dotype = ajtodo_post("dotype", "");
		$todotypekey = ajtodo_post("ajtodo_todotype_key", "");
		$todotypename = ajtodo_post("ajtodo_todotype_name", "");
		$todotypecolor = ajtodo_post("ajtodo_todotype_color", "");
		$todotypedefault = ajtodo_post("ajtodo_todotype_default", "");
		//$todotypeleader = ajtodo_post("ajtodo_todotype_leader", "");
		if($ptodotype->addTodoType($ajtodo_dotype, $todotypekey, $todotypename, $todotypecolor, $todotypedefault)){
			ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=todotype&pid=".$prj->id);
		}else{
			$msg = $ptodotype->msg ? $ptodotype->msg : __("할일 타입 생성에 실패했습니다.","ajtodo");
			ajtodo_alert_go($msg, 
					"?page=ajtodo_admin_project&ptype=team&ajtodo_type=todotype&pid=".$prj->id);
		}
	}else{
		echo $ptodotype->addTodoTypeView($todotypekey);
	}
}
