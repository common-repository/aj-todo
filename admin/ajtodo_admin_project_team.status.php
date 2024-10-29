<?php
function ajtodo_admin_project_team_status($prj){
	include(AJTODO_PLUGIN_PATH . "inc/ajtodo_js.php");
	$act = ajtodo_get("act","list");
	switch($act){
		case "list":
			ajtodo_admin_project_team_status_list($prj);
			break;
		case "addstatus":
			ajtodo_admin_project_team_status_addstatus($prj);
			break;
		case "addrule":
			ajtodo_admin_project_team_status_rule($prj);
			break;
		case "del":
			ajtodo_admin_project_team_status_delconfirm($prj);
			break;
	}
}
function ajtodo_admin_project_team_status_delconfirm($prj){
	$statuskey = ajtodo_get("statuskey","");
	$dotype = ajtodo_post("dotype", "");
	$tostatuskey = ajtodo_post("tostatuskey", "");
	$pstatus = new AJTODO_ProjectStatus();
	$pstatus->project = $prj;
	if($dotype == "del"){
		$pstatus->delStatus($statuskey, $tostatuskey);
		ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=status&pid=".$prj->id);
	}else{
		echo $pstatus->delConfirmView($statuskey);
	}
}
function ajtodo_admin_project_team_status_list($prj){
	$pstatus = new AJTODO_ProjectStatus();
	$pstatus->project = $prj;
	echo $pstatus->statusListView();
}
function ajtodo_admin_project_team_status_addstatus($prj){
	$statuskey = isset($_GET["statuskey"]) ? sanitize_text_field($_GET["statuskey"]) : "";
	$pstatus = new AJTODO_ProjectStatus();
	$pstatus->project = $prj;
	if(isset($_POST["dotype"])){
		$ajtodo_dotype = isset($_POST["dotype"]) ? sanitize_text_field($_POST["dotype"]) : "";
		$statuskey = ajtodo_post("ajtodo_status_key", "");
		$statusname = ajtodo_post("ajtodo_status_name", "");
		$statusicon = ajtodo_post("ajtodo_status_icon", "");
		$statustype = ajtodo_post("ajtodo_status_type", "");
		if($pstatus->addStatus($ajtodo_dotype, 
					$statuskey, $statusname, 
					$statusicon, $statustype)){
			ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=status&pid=".$prj->id);
		}else{
			$msg = $pstatus->msg ? $pstatus->msg : __("상태 생성에 실패했습니다.","ajtodo");
			ajtodo_alert_go($msg, 
					"?page=ajtodo_admin_project&ptype=team&act=addstatus&ajtodo_type=status&pid=".$prj->id);
		}
	}else{
		echo $pstatus->addStatusView($statuskey);
	}
}
function ajtodo_admin_project_team_status_rule($prj){
	$statuskey = isset($_GET["statuskey"]) ? sanitize_text_field($_GET["statuskey"]) : "";
	$pstatus = new AJTODO_ProjectStatus();
	$pstatus->project = $prj;
	if(isset($_POST["dotype"])){
		$actionrole = isset($_POST["actionrole"]) ? sanitize_text_field($_POST["actionrole"]) : "";
		$pstatus->updateActionRole($statuskey, $actionrole);
		ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=status&pid=".$prj->id);
	}else{
		echo $pstatus->addRuleView($statuskey);
	}
}
