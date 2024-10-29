<?php
add_action('wp_ajax_ajtodo_team_todo_ajax', 'ajtodo_team_todo_ajax' );
add_action('wp_ajax_nopriv_ajtodo_team_todo_ajax', 'ajtodo_team_todo_ajax' );

function ajtodo_admin_project_team_todo($prj){
	$act = isset($_GET["act"]) ? sanitize_text_field($_GET["act"]) : "list";
	switch($act){
		case "list":
			ajtodo_admin_project_team_todo_list($prj);
			break;
		case "detailview":
			ajtodo_admin_project_team_todo_detailview($prj);
			break;
	}
}
function ajtodo_admin_project_team_todo_list($prj){
	$common = new AJTODO_Common();
	$common->loadCommon();
	$user = wp_get_current_user();

	$planinfo = new AJTODO_ProjectPlan();
	$planinfo->project = $prj;
	$startPlanId = $planinfo->getStartPlan();

	$filter_pid = isset($_GET['pid']) ? sanitize_text_field($_GET['pid']) : "";

	$ajtodo_start_user_filter = get_option('ajtodo_start_user_filter', "AR");
	$ajtodo_start_status_filter = get_option('ajtodo_start_status_filter', "");
	$ajtodo_auto_done_hidden = get_option('ajtodo_auto_done_hidden', "Y");
	$ajtodo_del_noconfirm = get_option('ajtodo_del_noconfirm', "Y");
	$ajtodo_direct_done = get_option('ajtodo_direct_done', "N");
	$ajtodo_direct_inprogress_status_id = get_option('ajtodo_direct_inprogress_status_id', "2");
	$ajtodo_noti_delay = get_option('ajtodo_noti_delay', "2000");
	$ajtodo_done_click = get_option('ajtodo_done_click', 1);
	$ajtodo_set_autoclosedetailview		= get_option('ajtodo_set_autoclosedetailview', "Y");
	$ajtodo_set_showcatitem				= get_option('ajtodo_set_showcatitem', "Y");
	$ajtodo_set_showtodokey				= get_option('ajtodo_set_showtodokey', "Y");

    wp_enqueue_script('ajtodo_team_todo_js', AJTODO_JS_PATH."ajtodo_todo_team.".AJTODO_JSMIN."js", array('jquery'), '1.0', true );
	wp_localize_script('ajtodo_team_todo_js', 'ajax_todo_info', array(
        'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajtodo-ajax-nonce'),
		'type' => "get_todolist",
		'freeze' => $prj->donedate ? "Y" : "",
		'locale' => get_user_locale(),
		'userid' => $user->ID,
		'isshortcode' => "",
		'uid' => "",
		'planid' => $startPlanId ? $startPlanId : "",
		'progressview' => "Y",
		'siteurl' => get_site_url(),
		'planview' => "N",
		'projecttype' => $prj->projecttype,
		'user_filter' => $ajtodo_start_user_filter,
		'done_click' => $ajtodo_done_click,
		'scope_status_filter' => $ajtodo_start_status_filter,
		'status_filter' => "",
		'auto_done_hidden' => $ajtodo_auto_done_hidden,
		'set_show_todokey' => $ajtodo_set_showtodokey,
		'set_show_category' => $ajtodo_set_showcatitem,
		'del_noconfirm' => $ajtodo_del_noconfirm,
		'direct_done' => $ajtodo_direct_done,
		'inprogress_status_id' => $ajtodo_direct_inprogress_status_id,
		'noti_delay' => $ajtodo_noti_delay,
		'filter_project' => $filter_pid,
		'set_autoclode_done' => $ajtodo_set_autoclosedetailview,
		'currentuserstatus' => json_encode($prj->currentuserstatus),
		'statuses' => json_encode($prj->statuses),
		'planperms' => json_encode($prj->planperms),
		'todoperms' => json_encode($prj->todoperms),
		'docperms' => json_encode($prj->docperms),
		'todotypes' => json_encode($prj->todotype),
		'category' => json_encode($prj->category),
		'members' => json_encode(AJTODO_User::getUsers($prj))
	));
	
	$todolist = new AJTODO_ProjectTodoList();
	$todolist->project = $prj;
	$todolist->init($common);
	$todo = new AJTODO_Todo();
	$todo->project = $prj;
	$todo->init($common);
	echo $todolist->js_templates;
	echo "<div id='ajtodoDivTABox' style='display:none;'>";
	wp_editor('', 'ajtodoTABox');
	echo "<button class='btn btn-primary btn-sm' id='ajtodoTABoxSave' style='margin-top:4px;'>".__("변경 사항 저장","ajtodo")."</button>";
	echo "</div>";
	if($prj->hasPerm("tp_todo_create") && !$prj->donedate){
		echo $todolist->getCreateTodoBox();
	}
	if(!isset($_COOKIE["hidehello"])) {
		//echo $todo->getView("Hello");
	}
	echo $todolist->getFilter();
	if($prj->hasPerm("tp_todo_viewlist")){
		echo $todolist->getUserProgressView();
		echo $todolist->getView();
	}
	$common->last();
}

function ajtodo_ajax_team_gettodolist($prj){
	$todolist = new AJTODO_ProjectTodoList();
	$todolist->project = $prj;
	$todolist->search = ajtodo_post("search","");
	$todolist->statuskey = ajtodo_post("statuskey","");
	$todolist->authorid = ajtodo_post("authorid","");
	$todolist->assignid = ajtodo_post("assignid","");
	$todolist->todotype = ajtodo_post("todotype","");
	$todolist->planid = ajtodo_post("planid","");
	$ret = $todolist->getList();
	if(!$ret["result"]){
		$ret["msg"] = __("할일 목록을 가져오는데 문제가 발생했습니다.","ajtodo");
	}
	wp_send_json(json_encode($ret));
}

function ajtodo_ajax_team_gettodotypes($prj){
	$common = new AJTODO_Common();
	$common->loadCommon();

	$todolist = new AJTODO_ProjectTodoList();
	$todolist->init($common);
	$todolist->project = $prj;
	$todolist->planid = ajtodo_post("planid","");
	$ret = $todolist->getTodoTypes();
	$ret["result"] = true;
	wp_send_json(json_encode($ret));
}

function ajtodo_ajax_team_getprogress($prj){
	$common = new AJTODO_Common();
	$common->loadCommon();

	$todolist = new AJTODO_ProjectTodoList();
	$todolist->init($common);
	$todolist->project = $prj;
	$todolist->planid = ajtodo_post("planid","");
	$ret = $todolist->getUserProgress();
	$ret["result"] = true;
	wp_send_json(json_encode($ret));
}

function ajtodo_ajax_team_createtodo($prj){
	$common = new AJTODO_Common();
	$common->loadCommon();

	$todo = new AJTODO_Todo();
	$todo->init($common);
	$todo->project = $prj;
	$todo->todotype = ajtodo_post("todotype","");
	$todo->planid = ajtodo_post("planid","");
	$todo->title = sanitize_text_field($_POST['title']);
	$todo->comment = "";
	if($todo->createTodo()){
		$ret["result"] = true;
		$ret["msg"] = $todo->msg;
		$ret["todoid"] = $todo->id;
	}else{
		$ret["result"] = false;
		$ret["msg"] = $todo->msg;
	}
	wp_send_json(json_encode($ret));
}

function ajtodo_ajax_team_update($prj){
	$common = new AJTODO_Common();
	$common->loadCommon();

	$todo = new AJTODO_Todo();
	$todo->init($common);
	$todo->project = $prj;
	$todo->id = ajtodo_post('todo_id', '');
	$col = ajtodo_post('col', '');
	$val = ($col == "comment") ? wp_kses_post($_POST['val']) :  ajtodo_post('val', '');
	$colval = ajtodo_post('colval', '');
	if($todo->updateTodo($col, $val, $colval)){
		$ret["result"] = true;
		$ret["msg"] = $todo->msg;
		$ret["todoid"] = $todo->id;
	}else{
		$ret["result"] = false;
		$ret["msg"] = $todo->msg;
	}
	wp_send_json(json_encode($ret));
}

function ajtodo_plan_ajax_start($prj, $isstart){
	$plan = new AJTODO_ProjectPlan();
	$plan->project = $prj;
	$plan->id = ajtodo_post('planid', '');
	if($plan->startPlan($isstart)){
		$ret["result"] = true;
	}else{
		$ret["result"] = false;
		$ret["msg"] = $plan->msg;
	}
	wp_send_json(json_encode($ret));
}

function ajtodo_ajax_planorder(){
	$plan = new AJTODO_ProjectPlan();
	$planid = ajtodo_post('planid', '');
	$orders = ajtodo_post('orders', '');
	$plan->updateTodoOrder($planid, $orders);
	$ret["result"] = true;
	wp_send_json(json_encode($ret));
}

function ajtodo_team_todo_ajax(){
	$ret = array();
	$nonce = sanitize_text_field($_POST['nonce']);
	if(!wp_verify_nonce($nonce, 'ajtodo-ajax-nonce')){
		$ret["result"] = false;
		$ret["msg"] = __("비정상적인 접근입니다.","ajtodo");
		wp_send_json(json_encode($ret));
		exit;
	}

	$pid = ajtodo_post('pid', '');
	$prj = new AJTODO_Project();
	$prj->id = $pid;
	$prj->setProject();

	switch($_POST["type"]){
		case "gettodolist" :
			ajtodo_ajax_team_gettodolist($prj);
			break;
		case "createtodo" :
			ajtodo_ajax_team_createtodo($prj);
			break;
		case "getprogress" :
			ajtodo_ajax_team_getprogress($prj);
			break;
		case "gettodotypes" :
			ajtodo_ajax_team_gettodotypes($prj);
			break;
		case "updatetodo" :
			ajtodo_ajax_team_update($prj);
			break;
		case "getplanlist" :
			ajtodo_plan_ajax_list($prj);
			break;
		case "planstart" :
			ajtodo_plan_ajax_start($prj, true);
			break;
		case "planstop" :
			ajtodo_plan_ajax_start($prj, false);
			break;
		case "planorder" :
			ajtodo_ajax_planorder();
			break;

		case "deltodo" :
			ajtodo_ajax_deltodo();
			break;
		case "getprojects" :
			ajtodo_ajax_getprojects();
			break;
		case "setcookie" :
			ajtodo_ajax_setcookie();
			break;
	}
}
