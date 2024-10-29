<?php
function ajtodo_admin_project_team_category($prj){
	include(AJTODO_PLUGIN_PATH . "inc/ajtodo_js.php");
	$act = isset($_GET["act"]) ? sanitize_text_field($_GET["act"]) : "list";
	switch($act){
		case "list":
			ajtodo_admin_project_team_category_list($prj);
			break;
		case "addcategory":
			ajtodo_admin_project_team_category_addcategory($prj);
			break;
		case "del":
			ajtodo_admin_project_team_category_delconfirm($prj);
			break;
	}
}
function ajtodo_admin_project_team_category_list($prj){
	$pmem = new AJTODO_ProjectCategory();
	$pmem->project = $prj;
	echo $pmem->categoryListView();
}

function ajtodo_admin_project_team_category_delconfirm($prj){
	$categorykey = isset($_GET["categorykey"]) ? sanitize_text_field($_GET["categorykey"]) : "";
	$dotype = ajtodo_post("dotype", "");
	$pmem = new AJTODO_ProjectCategory();
	$pmem->project = $prj;
	if($dotype == "del"){
		$pmem->delCategory($categorykey);
		ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=category&pid=".$prj->id);
	}else{
		echo $pmem->delConfirmView($categorykey);
	}
}

function ajtodo_admin_project_team_category_addcategory($prj){
	$categorykey = isset($_GET["categorykey"]) ? sanitize_text_field($_GET["categorykey"]) : "";
	$pcategory = new AJTODO_ProjectCategory();
	$pcategory->project = $prj;
	if(isset($_POST["dotype"])){
		$ajtodo_dotype = ajtodo_post("dotype", "");
		$categorykey = ajtodo_post("ajtodo_category_key", "");
		$categoryname = ajtodo_post("ajtodo_category_name", "");
		$categorycolor = ajtodo_post("ajtodo_category_color", "");
		$categoryleader = ajtodo_post("ajtodo_category_leader", "");
		if($pcategory->addCategory($ajtodo_dotype, 
					$categorykey, $categoryname, 
					$categorycolor, $categoryleader)){
			ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=category&pid=".$prj->id);
		}else{
			$msg = $pcategory->msg ? $pcategory->msg : __("카테고리 생성에 실패했습니다.","ajtodo");
			ajtodo_alert_go($msg,
					"?page=ajtodo_admin_project&ptype=team&act=addcategory&ajtodo_type=category&pid=".$prj->id);
		}
	}else{
		echo $pcategory->addCategoryView($categorykey);
	}
}
