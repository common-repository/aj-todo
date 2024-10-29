<?php
add_action('wp_ajax_ajtodo_todo_ajax', 'ajtodo_todo_ajax' );
add_action('wp_ajax_nopriv_ajtodo_todo_ajax', 'ajtodo_todo_ajax' );

function ajtodo_todolist($atts){
	ajtodo_css_load();
	$pid = isset($atts["project"]) ? trim($atts["project"]) : "";
	$startPlanId = isset($atts["plan"]) ? trim($atts["plan"]) : "";
	
    $uid = isset($atts["uid"]) ? trim($atts["uid"]) : "";
    $pid = isset($atts["project"]) ? trim($atts["project"]) : "";
    $ajtodo_start_status_filter = isset($atts["status"]) ? trim($atts["status"]) : "";
    $search = isset($atts["search"]) ? trim($atts["search"]) : "";

	$prj = new AJTODO_Project();
	$prj->id = $pid;
	$prj->setProject();

	$common = new AJTODO_Common();
	$common->loadCommon();
	$user = wp_get_current_user();

	$ajtodo_auto_done_hidden = get_option('ajtodo_auto_done_hidden', "Y");
	$ajtodo_del_noconfirm = get_option('ajtodo_del_noconfirm', "Y");
	$ajtodo_direct_done = get_option('ajtodo_direct_done', "N");
	$ajtodo_direct_inprogress_status_id = get_option('ajtodo_direct_inprogress_status_id', "2");
	$ajtodo_noti_delay = get_option('ajtodo_noti_delay', "2000");
	$ajtodo_set_showtodokey				= get_option('ajtodo_set_showtodokey', "Y");

    wp_enqueue_script('ajtodo_todo_js', AJTODO_JS_PATH."ajtodo_todo_team.".AJTODO_JSMIN."js", array('jquery'), '1.0', true );
	wp_localize_script('ajtodo_todo_js', 'ajax_todo_info', array(
        'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajtodo-ajax-nonce'),
		'type' => "get_todolist",
		'locale' => get_locale(),
		'isshortcode' => "Y",
		'uid' => $uid,
		'user_filter' => "AR",
		'planid' => $startPlanId ? $startPlanId : "",
		'freeze' => "Y",
		'status_filter' => $ajtodo_start_status_filter,
		'auto_done_hidden' => $ajtodo_auto_done_hidden,
		'del_noconfirm' => $ajtodo_del_noconfirm,
		'direct_done' => $ajtodo_direct_done,
		'set_show_todokey' => $ajtodo_set_showtodokey,
		'inprogress_status_id' => $ajtodo_direct_inprogress_status_id,
		'noti_delay' => $ajtodo_noti_delay,
		'statuses' => json_encode($prj->statuses),
		'planperms' => json_encode($prj->planperms),
		'todoperms' => json_encode($prj->todoperms),
		'todotypes' => json_encode($prj->todotype),
		'category' => json_encode($prj->category),
		'members' => json_encode(AJTODO_User::getUsers($prj)),
		'filter_project' => $pid
	));

	//include(AJTODO_PLUGIN_PATH . "inc/ajtodo_js.php");
	$todolist = new AJTODO_ProjectTodoList();
	$todolist->init($common);
	$todolist->uid = $uid;
	$todo = new AJTODO_Todo();
	$todo->init($common);
	$ret = $todolist->js_templates;
	$ret .= "<div class='ajtodo'>";
	//$ret .= $todolist->getUserProgressView();
	$ret .= $todolist->getView();
	$ret .= "</div>";
	return $ret;
}

function ajtodo_admin_todo(){
	$common = new AJTODO_Common();
	$common->loadCommon();
	$common->start("container");
	$user = wp_get_current_user();
	$filter_pid = isset($_GET['pid']) ? sanitize_text_field($_GET['pid']) : "";

	$prj = new AJTODO_Project();
	$prj->id = $filter_pid;
	$prj->setProject();

	$ajtodo_start_user_filter = get_option('ajtodo_start_user_filter', "AR");
	$ajtodo_start_status_filter = get_option('ajtodo_start_status_filter', "O");
	$ajtodo_auto_done_hidden = get_option('ajtodo_auto_done_hidden', "Y");
	$ajtodo_del_noconfirm = get_option('ajtodo_del_noconfirm', "Y");
	$ajtodo_direct_done = get_option('ajtodo_direct_done', "N");
	$ajtodo_direct_inprogress_status_id = get_option('ajtodo_direct_inprogress_status_id', "2");
	$ajtodo_noti_delay = get_option('ajtodo_noti_delay', "2000");
	$ajtodo_done_click = get_option('ajtodo_done_click', 1);

    wp_enqueue_script('ajtodo_todo_js', AJTODO_JS_PATH."ajtodo_todo.".AJTODO_JSMIN."js", array('jquery'), '1.0', true );
	wp_localize_script('ajtodo_todo_js', 'ajax_todo_info', array(
        'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajtodo-ajax-nonce'),
		'type' => "get_todolist",
		'freeze' => $prj->donedate ? "Y" : "",
		'siteurl' => get_site_url(),
		'locale' => get_locale(),
		'userid' => $user->ID,
		'hello_title' => $user->user_nicename."! ".__("안녕하세요!","ajtodo"),
		'hello_msg' => __("할일들을 체계적으로 관리해보세요~<br/>먼저 해야할 일들을 등록해보세요.","ajtodo"),
		'uid' => "",
		'user_filter' => $ajtodo_start_user_filter,
		'done_click' => $ajtodo_done_click,
		'status_filter' => $ajtodo_start_status_filter,
		'auto_done_hidden' => $ajtodo_auto_done_hidden,
		'del_noconfirm' => $ajtodo_del_noconfirm,
		'direct_done' => $ajtodo_direct_done,
		'inprogress_status_id' => $ajtodo_direct_inprogress_status_id,
		'noti_delay' => $ajtodo_noti_delay,
		'filter_project' => $filter_pid
	));

	echo "<div class='row'><div class='col'><h1>".__("할일", "ajtodo")."</h1></div><div class='col text-right'>";
	echo "<div class='font-weight-bold'>".__("Shortcode","ajtodo")."</div>";
	echo "<div id='ajtodo_todo_sc'></div>";
	echo "</div></div>";
	$todolist = new AJTODO_TodoList();
	$todolist->init($common);
	$todo = new AJTODO_Todo();
	$todo->init($common);
	if(!$prj->donedate){
		echo $todo->getView("CreateTodo");
	}
	if(!isset($_COOKIE["hidehello"])) {
		echo $todo->getView("Hello");
	}
	echo $todolist->getFilter();
	echo $todolist->getUserProgressView();
	echo $todolist->getView();
	$common->last();
}

function ajtodo_ajax_gettodolist(){
	$todo = new AJTODO_TodoList();
	$todo->uid = isset($_POST['uid']) ? sanitize_text_field($_POST['uid']) : "";
	$todo->filter_project = isset($_POST['filter_project']) ? sanitize_text_field($_POST['filter_project']) : "";
	$todo->filter_user = isset($_POST['filter_user']) ? sanitize_text_field($_POST['filter_user']) : "";
	$todo->filter_status = isset($_POST['filter_status']) ? sanitize_text_field($_POST['filter_status']) : "";
	$todo->search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : "";
	$ret = $todo->getList();
	if(!$ret["result"]){
		$ret["msg"] = __("할일 목록을 가져오는데 문제가 발생했습니다.","ajtodo");
	}
	wp_send_json(json_encode($ret));
}

function ajtodo_ajax_gettodo(){
	$todo = new AJTODO_Todo();
	$todo->id = sanitize_text_field($_POST['todo_id']);
	$todo->projectid = sanitize_text_field($_POST['pid']);
	//$prj->comment = sanitize_text_field($_POST['comment']);
	//$prj->projecttype = sanitize_text_field($_POST['projecttype']);
	if($todo->getTodoInfo()){
		$ret["result"] = true;
		$ret["msg"] = $todo->msg;
		$ret["data"] = $todo->tData;
	}else{
		$ret["result"] = false;
		$ret["msg"] = $todo->msg;
	}
	wp_send_json(json_encode($ret));
}

function ajtodo_ajax_createtodo(){
	$common = new AJTODO_Common();
	$common->loadCommon();

	$todo = new AJTODO_Todo();
	$todo->init($common);
	$todo->title = sanitize_text_field($_POST['title']);
	$todo->projectid = sanitize_text_field($_POST['projectid']);
	$todo->project_filter = sanitize_text_field($_POST['project_filter']);
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

function ajtodo_ajax_updatestatus(){
	$common = new AJTODO_Common();
	$common->loadCommon();

	$todo = new AJTODO_Todo();
	$todo->init($common);
	$todo->id = sanitize_text_field($_POST['todo_id']);
	$status_id = sanitize_text_field($_POST['status_id']);
	if($todo->updateStatus($status_id)){
		$ret["result"] = true;
		$ret["msg"] = $todo->msg;
		$ret["todoid"] = $todo->id;
	}else{
		$ret["result"] = false;
		$ret["msg"] = $todo->msg;
	}
	wp_send_json(json_encode($ret));
}

function ajtodo_ajax_update(){
	$common = new AJTODO_Common();
	$common->loadCommon();

	$todo = new AJTODO_Todo();
	$todo->init($common);
	$todo->id = sanitize_text_field($_POST['todo_id']);
	$todo->projectid = sanitize_text_field($_POST['pid']);
	$col = isset($_POST['col']) ? sanitize_text_field($_POST['col']) : "";

	if($col == "comment"){
		$val = isset($_POST['val']) ? wp_kses_post($_POST['val']) : "";
	}else{
		$val = isset($_POST['val']) ? sanitize_text_field($_POST['val']) : "";
	}
	
	$colval = isset($_POST['colval']) ? sanitize_text_field($_POST['colval']) : "";
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

function ajtodo_ajax_deltodo(){
	$common = new AJTODO_Common();
	$common->loadCommon();

	$todo = new AJTODO_Todo();
	$todo->init($common);
	$todo->id = sanitize_text_field($_POST['todo_id']);
	$todo->projectid = sanitize_text_field($_POST['pid']);
	if($todo->delTodo()){
		$ret["result"] = true;
		$ret["msg"] = $todo->msg;
		$ret["todoid"] = $todo->id;
	}else{
		$ret["result"] = false;
		$ret["msg"] = $todo->msg;
	}
	wp_send_json(json_encode($ret));
}

function ajtodo_ajax_getprogress(){
	$common = new AJTODO_Common();
	$common->loadCommon();

	$todolist = new AJTODO_TodoList();
	$todolist->init($common);
	$todolist->uid = isset($_POST['uid']) ? sanitize_text_field($_POST['uid']) : "";
	$todolist->planid = ajtodo_post("planid","");
	$todolist->filter_project = isset($_POST['filter_project']) ? sanitize_text_field($_POST['filter_project']) : "";
	$ret = $todolist->getUserProgress();
	$ret["result"] = true;
	wp_send_json(json_encode($ret));
}

function ajtodo_ajax_getprojects(){
	$common = new AJTODO_Common();
	$common->loadCommon();

	$prjList = new AJTODO_ProjectList();
	$prjList->getList(true);
	$ret["result"] = true;
	$ret["projects"] = $prjList->plist;
	wp_send_json(json_encode($ret));
}

function ajtodo_ajax_setcookie(){
	$key = sanitize_text_field($_POST['key']);
	$val = sanitize_text_field($_POST['val']);
	$expiry = strtotime('+1 day');
	setcookie($key, $val, $expiry, COOKIEPATH, COOKIE_DOMAIN);
	$ret["result"] = true;
	wp_die();
}
function ajtodo_ajax_getdoc(){
	$pdoc = new AJTODO_ProjectLink();
	$data = $pdoc->getDocs($_POST['pid'], "", $_POST['todo_id']);
	$ret["result"] = true;
	$ret["docs"] = $data;
	wp_send_json(json_encode($ret));
}
function ajtodo_todo_ajax(){
	$ret = array();
	$nonce = sanitize_text_field($_POST['nonce']);
	if(!wp_verify_nonce($nonce, 'ajtodo-ajax-nonce')){
		$ret["result"] = false;
		$ret["msg"] = __("비정상적인 접근입니다.","ajtodo");
	}
	switch($_POST["type"]){
		case "gettodolist" :
			ajtodo_ajax_gettodolist();
			break;
		case "createtodo" :
			ajtodo_ajax_createtodo();
			break;
		case "getsingletodo" :
			ajtodo_ajax_gettodo();
			break;
		case "updatetodo" :
			ajtodo_ajax_update();
			break;
		case "updatestatus" :
			ajtodo_ajax_updatestatus();
			break;
		case "deltodo" :
			ajtodo_ajax_deltodo();
			break;
		case "getdocs" :
			ajtodo_ajax_getdoc();
			break;
		case "getprogress" :
			ajtodo_ajax_getprogress();
			break;
		case "getprojects" :
			ajtodo_ajax_getprojects();
			break;
		case "setcookie" :
			ajtodo_ajax_setcookie();
			break;
	}
}
