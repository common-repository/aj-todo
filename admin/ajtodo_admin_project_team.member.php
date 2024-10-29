<?php
function ajtodo_admin_project_team_member($prj){
	$act = isset($_GET["act"]) ? sanitize_text_field($_GET["act"]) : "list";
	switch($act){
		case "list":
			ajtodo_admin_project_team_member_list($prj);
			break;
		case "addrole":
			ajtodo_admin_project_team_member_addrole($prj);
			break;
		case "addmember":
			ajtodo_admin_project_team_member_member($prj);
			break;
	}
}
function ajtodo_admin_project_team_member_list($prj){
	$pmem = new AJTODO_ProjectMember();
	$pmem->project = $prj;
	echo $pmem->memberListView();
}
function ajtodo_admin_project_team_member_addrole($prj){
	$rolekey = isset($_GET["rolekey"]) ? sanitize_text_field($_GET["rolekey"]) : "";
	$pmem = new AJTODO_ProjectMember();
	$pmem->project = $prj;
	if(isset($_POST["dotype"])){
		$ajtodo_dotype = isset($_POST["dotype"]) ? sanitize_text_field($_POST["dotype"]) : "";
		$ajtodo_rolekey = isset($_POST["ajtodo_role_key"]) ? sanitize_text_field($_POST["ajtodo_role_key"]) : "";
		$ajtodo_rolename = isset($_POST["ajtodo_role_name"]) ? sanitize_text_field($_POST["ajtodo_role_name"]) : "";
		if($ajtodo_dotype == "del"){
			if($pmem->delRole($ajtodo_rolekey)){
				ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=member&pid=".$prj->id);
			}else{
				ajtodo_alert_go(__("역할 생성에 실패했습니다.","ajtodo"), 
						"?page=ajtodo_admin_project&ptype=team&act=addrole&ajtodo_type=member&pid=".$prj->id);
			}
		}else{
			if($pmem->addRole($ajtodo_dotype, $ajtodo_rolekey, $ajtodo_rolename)){
				ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=member&pid=".$prj->id);
			}else{
			$msg = $pmem->msg ? $pmem->msg : __("역할 생성에 실패했습니다.","ajtodo");
				ajtodo_alert_go($msg, 
						"?page=ajtodo_admin_project&ptype=team&act=addrole&ajtodo_type=member&pid=".$prj->id);
			}
		}
	}else{
		echo $pmem->addRoleView($rolekey);
	}
}
function ajtodo_admin_project_team_member_member($prj){
	$rolekey = isset($_GET["rolekey"]) ? sanitize_text_field($_GET["rolekey"]) : "";
	$pmem = new AJTODO_ProjectMember();
	$pmem->project = $prj;
	if(isset($_POST["dotype"])){
		$ajtodo_users = isset($_POST["ajtodo_users"]) ? sanitize_text_field($_POST["ajtodo_users"]) : "";
		$ajtodo_role = isset($_POST["role"]) ? sanitize_text_field($_POST["role"]) : "";
		$pmem->addMemberRole($ajtodo_role, $ajtodo_users);
		ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=member&pid=".$prj->id);
	}else{
		echo $pmem->addMemberView($rolekey);
	}
}
