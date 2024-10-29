<?php
add_action('wp_ajax_ajtodo_project_ajax', 'ajtodo_project_ajax' );
add_action('wp_ajax_nopriv_ajtodo_project_ajax', 'ajtodo_project_ajax' );
function ajtodo_admin_project_private(){
	$ajtodo_type = ajtodo_get("ajtodo_type", "list");
	$pid = ajtodo_get("id", "");
	$common = new AJTODO_Common();
	$common->loadCommon();
	$common->start("container");
    //wp_enqueue_style('ajtodo_main_css', AJTODO_PLUGIN_URL."css/ajtodo.css");
    wp_enqueue_script('ajtodo_project_js', AJTODO_JS_PATH."ajtodo_project.".AJTODO_JSMIN."js", array('jquery'), '1.0', true );
	wp_localize_script('ajtodo_project_js', 'ajax_project_info', array(
        'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajtodo-ajax-nonce'),
		'type' => $ajtodo_type,
		'pid' => $pid
	));
	switch($ajtodo_type){
		case "list":
			ajtodo_admin_project_list($common);
			break;
		case "add":
			ajtodo_admin_project_add($common, "add");
			break;
		case "edit":
			ajtodo_admin_project_add($common, "edit");
			break;
		case "del":
			ajtodo_admin_project_del($common);
			break;
		case "done":
			ajtodo_admin_project_done($common);
			break;
	}
	$common->last();
}

function ajtodo_admin_project_done($common){
	$prj = new AJTODO_Project();
	$prj->init($common);
	$prj->id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : "";
	echo "<h1>".__("프로젝트", "ajtodo")."</h1>";
	echo $prj->getDoneConfirmView();
}

function ajtodo_project_active(){
	$ret = array();
	$prj = new AJTODO_Project();
	$prj->id = sanitize_text_field($_POST['pid']);
	if($prj->activeProject()){
		$ret["result"] = true;
		$ret["msg"] = __("프로젝트 활성화 완료","ajtodo");
	}else{
		$ret["result"] = false;
		$ret["msg"] = __("프로젝트 활성화 처리에 실패하였습니다.","ajtodo");
	}
	wp_send_json(json_encode($ret));
}

function ajtodo_admin_project_del($common){
	$prj = new AJTODO_Project();
	$prj->init($common);
	$prj->id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : "";
	echo "<h1>".__("프로젝트", "ajtodo")."</h1>";
	echo $prj->getDelConfirmView();
}

function ajtodo_admin_project_list($common){
	$prjList = new AJTODO_ProjectList();
	$prjList->init($common);
	$prjList->getList();
	echo "<h1>".__("프로젝트", "ajtodo")." ";
	if($prjList->canCreateProject()){
		echo $prjList->getButton("CreateProject");
	}
	echo "</h1>";
	echo $prjList->getView();
}

function ajtodo_admin_project_add($common, $type){
	$prj = new AJTODO_Project();
	$prj->init($common);
	if($type == "edit"){
		$prj->id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : "";
	}
	if($type == "add"){
		echo "<h1>".__("프로젝트 생성", "ajtodo")."</h1>";
	}else{
		echo "<h1>".__("프로젝트 수정", "ajtodo")."</h1>";
	}
	echo $prj->createForm();
}

function ajtodo_project_ajax(){
	$nonce = sanitize_text_field($_POST['nonce']);
	if(!wp_verify_nonce($nonce, 'ajtodo-ajax-nonce')){
		$ret["result"] = false;
		$ret["msg"] = __("비정상적인 접근입니다.","ajtodo");
	}
	$type = sanitize_text_field($_POST['type']);
	switch($type){
		case "add" : 
			ajtodo_create_project();
			break;
		case "edit" : 
			ajtodo_edit_project();
			break;
		case "getissuecount" : 
			ajtodo_project_todocount();
			break;
		case "delprojectreal" : 
			ajtodo_project_del();
			break;
		case "projectdone" : 
			ajtodo_project_done();
			break;
		case "activedone" : 
			ajtodo_project_active();
			break;
	}
}

function ajtodo_project_done(){
	$ret = array();
	$prj = new AJTODO_Project();
	$prj->id = sanitize_text_field($_POST['pid']);
	$prj->setProject();
	if($prj->doneProject()){
		$ret["result"] = true;
		$ret["msg"] = __("프로젝트 완료","ajtodo");
	}else{
		$ret["result"] = false;
		$ret["msg"] = __("프로젝트 완료 처리에 실패하였습니다.","ajtodo");
	}
	wp_send_json(json_encode($ret));
}

function ajtodo_project_todocount(){
	$ret = array();
	$prj = new AJTODO_Project();
	$prj->id = sanitize_text_field($_POST['pid']);
	$prj->getCount();
	$ret["result"] = true;
	$ret["totalissue"] = $prj->totalissue;
	$ret["doneissue"] = $prj->doneissue;
	$ret["msg"] = __("프로젝트 생성완료","ajtodo");
	wp_send_json(json_encode($ret));
}

function ajtodo_project_del(){
	$ret = array();
	$prj = new AJTODO_Project();
	$prj->id = sanitize_text_field($_POST['pid']);
	if($prj->delProject()){
		$ret["result"] = true;
		$ret["msg"] = __("프로젝트 삭제완료","ajtodo");
	}else{
		$ret["result"] = false;
		$ret["msg"] = __("프로젝트 삭제에 실패하였습니다.","ajtodo");
	}
	wp_send_json(json_encode($ret));
}

function ajtodo_create_project(){
	$ret = array();
	$prj = new AJTODO_Project();
	$prj->pkey = strtoupper(sanitize_text_field($_POST['pkey']));
	$prj->title = sanitize_text_field($_POST['title']);
	$prj->comment = sanitize_text_field($_POST['comment']);
	$prj->autoassign = sanitize_text_field($_POST['autoassign']);
	$prj->projecttype = sanitize_text_field($_POST['projecttype']);
	if($prj->createProject()){
		$ret["result"] = true;
		$ret["projectid"] = $prj->id;
		$ret["msg"] = __("프로젝트 생성완료","ajtodo");
	}else{
		$ret["result"] = false;
		$ret["msg"] = ($prj->msg ? $prj->msg :  __("프로젝트 생성에 실패하였습니다.","ajtodo"));
	}
	wp_send_json(json_encode($ret));
}

function ajtodo_edit_project(){
	$ret = array();
	$prj = new AJTODO_Project();
	$prj->id = sanitize_text_field($_POST['pid']);
	$prj->title = sanitize_text_field($_POST['title']);
	$prj->comment = sanitize_textarea_field($_POST['comment']);
	$prj->autoassign = sanitize_text_field($_POST['autoassign']);
	$prj->projecttype = sanitize_text_field($_POST['projecttype']);
	if($prj->editProject()){
		$prj->setProject();
		$ret["result"] = true;
		$ret["pid"] = $prj->id;
		$ret["projecttype"] = $prj->projecttype;
		$ret["msg"] = __("프로젝트 수정완료","ajtodo");
	}else{
		$ret["result"] = false;
		$ret["msg"] = __("프로젝트 수정에 실패하였습니다.","ajtodo");
	}
	wp_send_json(json_encode($ret));
}
