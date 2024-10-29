<?php
//add_action('wp_dashboard_setup', 'ajtodo_dashboard_todo_widgets');
function ajtodo_dashboard_todo_widgets() {
	global $wp_meta_boxes;
	wp_add_dashboard_widget('ajtodo_todo_widget', __("빠른 할일 등록","ajtodo"), 'ajtodo_dashboard_todo_quick');
}
function ajtodo_dashboard_todo_quick() {
	$common = new AJTODO_Common();
	$common->loadCommon();
	$user = wp_get_current_user();
    wp_enqueue_script('ajtodo_todo_js', AJTODO_JS_PATH."ajtodo_todo.".AJTODO_JSMIN."js", array('jquery'), '1.0', true );
	wp_localize_script('ajtodo_todo_js', 'ajax_todo_info', array(
        'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajtodo-ajax-nonce'),
		'type' => "createtodo",
	));

	$prjList = new AJTODO_ProjectList();
	$prjList->getList(true);
	echo $common->js_templates;
?>
	<div class="ajtodo">
	<input type='text' class='form-control form-control-sm' style='margin-bottom:8px;'
		name='title'
		id='ajtodo_title' placeholder='<?php echo __("할일을 입력해주세요.","ajtodo")?>'>
	<select name="projectid" id="ajtodo_projectid" class='form-control form-control-sm' style='margin-bottom:8px;height: 30px;'>
		<option value=""><?php echo __("프로젝트 없음","ajtodo")?></option>
		<?php foreach($prjList->plist as $p){?>
		<option value="<?php echo $p->id?>"><?php echo $p->title?></option>
		<?php }?>
	</select>
	<button class='btn btn-primary btn-sm' id='btnQuickTodo'><?php echo __("할일 등록하기","ajtodo")?></button>
	</div>
<?php
}
?>
