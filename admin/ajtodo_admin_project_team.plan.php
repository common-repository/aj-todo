<?php
function ajtodo_admin_project_team_plan($prj){
	$user = wp_get_current_user();
	$aj_planid = ajtodo_get("plankey", "");
	$ajtodo_type = ajtodo_get("ajtodo_type", "");

	$planinfo = new AJTODO_ProjectPlan();
	$planinfo->project = $prj;
	$startPlanId = $aj_planid ? $aj_planid : $planinfo->getStartPlan();

	$ajtodo_set_autoclosedetailview		= get_option('ajtodo_set_autoclosedetailview', "Y");
	$ajtodo_set_showcatitem				= get_option('ajtodo_set_showcatitem', "Y");
	$ajtodo_set_showtodokey				= get_option('ajtodo_set_showtodokey', "Y");
	$ajtodo_noti_delay = get_option('ajtodo_noti_delay', "2000");

	$nowplanorderset = "";
	if($ajtodo_type == "board"){
		wp_enqueue_script('jqueryui', AJTODO_JS_PATH."jquery-ui.min.js", array('jquery'), '1.0', true );
		//wp_enqueue_script('ajtodo_team_board_js', AJTODO_JS_PATH."ajtodo_board.".AJTODO_JSMIN."js", array('jquery'), '1.0', true );
		foreach($prj->plan as $p){
			if($p["id"] == ajtodo_get("plankey", "")){
				$nowplanorderset = $p["todoorderset"];
				break;
			}
		}
	}

    wp_enqueue_script('ajtodo_team_todo_js', AJTODO_JS_PATH."ajtodo_todo_team.".AJTODO_JSMIN."js", array('jquery'), '1.0', true );
	wp_localize_script('ajtodo_team_todo_js', 'ajax_todo_info', array(
        'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajtodo-ajax-nonce'),
		'type' => "get_todolist",
		'freeze' => $prj->donedate ? "Y" : "",
		'locale' => get_user_locale(),
		'userid' => $user->ID,
		'planid' => $startPlanId ? $startPlanId : "0",
		'planorderset' => $ajtodo_type == "board" ? $nowplanorderset : "",
		'siteurl' => get_site_url(),
		'view' => ($ajtodo_type == "board" ? "board" : ""),
		'progressview' => "N",
		'isshortcode' => "",
		'planview' => "Y",
		'scope_status_filter' => "OI",
		'projecttype' => $prj->projecttype,
		'filter_project' => $prj->id,
		'noti_delay' => $ajtodo_noti_delay / 10,
		'set_autoclode_done' => $ajtodo_set_autoclosedetailview,
		'set_show_category' => $ajtodo_set_showcatitem,
		'set_show_todokey' => $ajtodo_set_showtodokey,
		'currentuserstatus' => json_encode($prj->currentuserstatus),
		'statuses' => json_encode($prj->statuses),
		'planperms' => json_encode($prj->planperms),
		'todoperms' => json_encode($prj->todoperms),
		'todotypes' => json_encode($prj->todotype),
		'category' => json_encode($prj->category),
		'docperms' => json_encode($prj->docperms),
		'members' => json_encode(AJTODO_User::getUsers($prj))
	));
	if($ajtodo_type == "board"){
		ajtodo_admin_project_team_plan_board($prj, $nowplanorderset);
	}else{
		$act = isset($_GET["act"]) ? sanitize_text_field($_GET["act"]) : "list";
		switch($act){
			case "list":
				ajtodo_admin_project_team_plan_list($prj);
				break;
			case "addplan":
				ajtodo_admin_project_team_plan_addplan($prj);
				break;
			case "finish":
				ajtodo_admin_project_team_plan_finish($prj);
				break;
			case "del":
				ajtodo_admin_project_team_plan_delconfirm($prj);
				break;
		}
	}
}
function ajtodo_admin_project_team_plan_board($prj, $nowplanorderset){
	$common = new AJTODO_Common();
	$common->loadCommon();
	echo $common->js_templates;
	echo "<div id='ajtodo_todoview_wrap' style='display:none;'><div id='ajtodo_todoview'></div></div>";

	$pmem = new AJTODO_ProjectPlan();
	$pmem->project = $prj;
	include(AJTODO_PLUGIN_PATH . "inc/ajtodo_js.php");
	echo "<div id='ajtodoDivTABox' style='display:none;'>";
	wp_editor('', 'ajtodoTABox');
	echo "<button class='btn btn-primary btn-sm' id='ajtodoTABoxSave' style='margin-top:4px;'>".__("변경 사항 저장","ajtodo")."</button>";
	echo "</div>";
	echo $pmem->getTodoModal();

	/*
	if($prj->hasPerm("tp_todo_create") && !$prj->donedate){
		$todolist = new AJTODO_ProjectTodoList();
		$todolist->project = $prj;
		$todolist->init($common);
		echo $todolist->getCreateTodoBox();
	}
	*/

	echo $pmem->setPlanBoard($nowplanorderset);

}

function ajtodo_admin_project_team_plan_list($prj){
	$pmem = new AJTODO_ProjectPlan();
	$pmem->project = $prj;
	include(AJTODO_PLUGIN_PATH . "inc/ajtodo_js.php");
	echo $pmem->planListViewAjax(ajtodo_admin_project_plan_todo_list($prj));
}

function ajtodo_plan_info($atts){
	$pid = isset($atts["project"]) ? trim($atts["project"]) : "";
	$planid = isset($atts["plan"]) ? trim($atts["plan"]) : "";
	$common = new AJTODO_Common();
	$common->loadCommon();

	$prj = new AJTODO_Project();
	$prj->id = $pid;
	$prj->setProject();

	$pmem = new AJTODO_ProjectPlan();
	$pmem->project = $prj;
	$ret = "<div class='ajtodo'>";
	$ret .= $pmem->showPlanInfo($planid);
	$ret .= "</div>";
	return $ret;
}

function ajtodo_admin_project_plan_todo_list($prj){
	$common = new AJTODO_Common();
	$common->loadCommon();
	
	$todolist = new AJTODO_ProjectTodoList();
	$todolist->project = $prj;
	$todolist->init($common);
	echo $todolist->js_templates;
	echo "<div id='ajtodoDivTABox' style='display:none;'>";
	wp_editor('', 'ajtodoTABox');
	echo "<button class='btn btn-primary btn-sm' id='ajtodoTABoxSave' style='margin-top:4px;'>".__("변경 사항 저장","ajtodo")."</button>";
	echo "</div>";
	
	$todo = new AJTODO_Todo();
	$todo->project = $prj;
	$todo->init($common);

	$ret = "";
	if($prj->hasPerm("tp_todo_create") && !$prj->donedate){
		$ret .= $todolist->getCreateTodoBox();
	}
	
	$ret .= "<div class='' id='ajtodo_top_filter_box_empty' style='display:none;'>";
	$ret .= "<div class='alert alert-warning'>".__("할일이 없네요. 프로젝트에 할일을 등록해보세요.","ajtodo")."</div>";
	$ret .= "</div>";
	
	if($prj->hasPerm("tp_todo_viewlist")){
		$ret .= $todolist->getView();
	}
	return $ret;
}

function ajtodo_admin_project_team_plan_delconfirm($prj){
	$plankey = isset($_GET["plankey"]) ? sanitize_text_field($_GET["plankey"]) : "";
	$dotype = ajtodo_post("dotype", "");
	$pmem = new AJTODO_ProjectPlan();
	$pmem->project = $prj;
	if($dotype == "del"){
		$pmem->delPlan($plankey);
		ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=plan&pid=".$prj->id);
	}else{
		echo $pmem->delConfirmView($plankey);
	}
}

function ajtodo_admin_project_team_plan_finish($prj){
	$plankey = isset($_GET["plankey"]) ? sanitize_text_field($_GET["plankey"]) : "";
	$pplan = new AJTODO_ProjectPlan();
	$pplan->project = $prj;
	if(isset($_POST["dotype"])){
		$ajtodo_dotype = ajtodo_post("dotype", "");
		if($pplan->finish($plankey)){
			ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=plan&pid=".$prj->id);
		}else{
			ajtodo_alert_go(__("플랜 생성에 실패했습니다.","ajtodo"), 
					"?page=ajtodo_admin_project&ptype=team&act=list&ajtodo_type=plan&pid=".$prj->id);
		}
	}else{
		echo $pplan->finishConfirmView($plankey);
	}
}

function ajtodo_admin_project_team_plan_addplan($prj){
	$common = new AJTODO_Common();
	$common->loadCommon();
	include(AJTODO_PLUGIN_PATH . "inc/ajtodo_js.php");
	echo $common->js_templates;

	$plankey = isset($_GET["plankey"]) ? sanitize_text_field($_GET["plankey"]) : "";
	$pplan = new AJTODO_ProjectPlan();
	$pplan->project = $prj;
	if(isset($_POST["dotype"])){
		$ajtodo_dotype = ajtodo_post("dotype", "");
		$plankey = ajtodo_post("ajtodo_plan_key", "");
		$plantitle = ajtodo_post("ajtodo_plan_title", "");
		$plancomment = isset($_POST["plancomment"]) ? sanitize_textarea_field($_POST["plancomment"]) : "";
		$startdate = ajtodo_post("startdate", "");
		$finishdate = ajtodo_post("finishdate", "");
		if($pplan->addPlan($ajtodo_dotype, 
					$plankey, $plantitle, $plancomment,
					$startdate, $finishdate)){
			ajtodo_go("?page=ajtodo_admin_project&ptype=team&ajtodo_type=plan&pid=".$prj->id);
		}else{
			ajtodo_alert_go(__("플랜 생성에 실패했습니다.","ajtodo"), 
					"?page=ajtodo_admin_project&ptype=team&act=list&ajtodo_type=plan&pid=".$prj->id);
		}
	}else{
		echo $pplan->addPlanView($plankey);
	}
}

function ajtodo_plan_ajax_list($prj){
	$ret = array();
	$plan = new AJTODO_ProjectPlan();
	$plan->project = $prj;
	$plan->getTodoCountByPlans();

	$ret["planlist"] = $plan->project->plan;
	$ret["result"] = true;

	if(!$ret["result"]){
		$ret["msg"] = __("할일 목록을 가져오는데 문제가 발생했습니다.","ajtodo");
	}
	wp_send_json(json_encode($ret));
}

add_action("ajtodo_finish_plan", function($projectid, $planid){
	$prj = new AJTODO_Project();
	$prj->id = $projectid;
	$prj->setProject();

	$plan = new AJTODO_ProjectPlan();
	$plan->project = $prj;
	$plan->id = $planid;
	$plan->startPlan(false);
}, 10, 2);
