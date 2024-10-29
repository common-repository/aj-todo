<?php
function ajtodo_admin_project_team_role($prj){
	$prole = new AJTODO_ProjectRole();
	$prole->project = $prj;
	if(isset($_POST["dotype"])){
		if (!isset($_POST['update_roleperm']) || 
    		!wp_verify_nonce($_POST['update_roleperm'], 'ajtodo_nonce')){
			ajtodo_alert_go(__("잘못된 경로에서의 접근입니다.","ajtodo"), "?page=ajtodo_admin_project");
			return;
		}
		if($prole->updateRolePerms($_POST)){
			ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=role&pid=".$prj->id);
		}else{
			ajtodo_alert_go(__("처리중에 문제가 발생했습니다.","ajtodo"), 
					"?page=ajtodo_admin_project&ptype=team&ajtodo_type=role&pid=".$prj->id);
		}
	}else{
		echo $prole->createForm();
	}
}
