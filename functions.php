<?php
function ajtodo_post_textarea($key, $default){
	return isset($_POST[$key]) ? sanitize_textarea_field($_POST[$key]) : $default;
}
function ajtodo_post($key, $default){
	return isset($_POST[$key]) ? sanitize_text_field($_POST[$key]) : $default;
}
function ajtodo_get($key, $default){
	return isset($_GET[$key]) ? sanitize_text_field($_GET[$key]) : $default;
}
function ajtodo_go($url){
	echo "<script>window.location = '$url'</script>";
	exit;
}

function ajtodo_alert_go($msg, $url){
	echo "<script> alert('".$msg."'); window.location = '".$url."'; </script>";
	exit;
}

function ajtodo_T($obj, $html){
	$ret = $html;
	foreach($obj as $key => $value){
		if(!is_array($value)){
			$ret = str_replace("[T_".$key."]", $value, $ret);
			$ret = str_replace("[R_".$key."_".$value."]", "checked", $ret);
			if(!$value){
				$ret = str_replace("[V_".$key."_HIDE]", "style='display:none;'", $ret);
			}
		}
	}
	return $ret;
}

add_filter('document_title_parts', 'aj_change_page_title' );
function aj_change_page_title($title_parts) {
	global $wp;
	if(isset($wp->query_vars["todo_key"])){
		if($title_parts['title'] == get_bloginfo('name')){
			$todo_key = $wp->query_vars["todo_key"];
			$todo = new AJTODO_Todo();
			$todo->tkey = $todo_key;
			$todo->setProject($todo_key);
			$title_parts['title'] = "[".$todo_key."]".$todo->tData->title;
		}
	}
    return $title_parts;
}

add_action('wp_enqueue_scripts', 'ajtodo_jscss_load');
add_action('admin_enqueue_scripts', 'ajtodo_jscss_load');
function ajtodo_jscss_load() {

	ajtodo_css_load();
	
	wp_dequeue_script('moment');
	wp_dequeue_script('bootstrapjs');

	wp_register_script('ajtodo', AJTODO_JS_PATH.'ajtodo.'.AJTODO_JSMIN.'js', array('jquery'));
	wp_register_script('bootstrap-notify', AJTODO_JS_PATH.'bootstrap-notify.min.js', array('jquery'));
	wp_register_script('ajtodobootstrapbundlejs', 
		AJTODO_PLUGIN_URL.'bootstrap/js/bootstrap.bundle.min.js',
		array('jquery'),'1.5', true);
	wp_register_script('ajtodomoment', AJTODO_JS_PATH.'moment-with-locales.min.js', array('jquery'));

	wp_enqueue_script('ajtodo');
	wp_enqueue_script('bootstrap-notify');
	wp_enqueue_script('ajtodobootstrapbundlejs');
	wp_enqueue_script('ajtodomoment');
}

function ajtodo_css_load(){
	wp_register_style('ajtodomaincss', AJTODO_PLUGIN_URL.'css/ajtodo_bs.css');
	wp_register_style('ajtodocss', AJTODO_PLUGIN_URL.'css/ajtodo.css');
	wp_register_style('ajtoskincss', AJTODO_PLUGIN_URL.'skins/basic/ajtodo.css');
	wp_register_style('font-awesome', AJTODO_PLUGIN_URL.'css/all.min.css');

	wp_enqueue_style('font-awesome');
	wp_enqueue_style('ajtodomaincss');
	wp_enqueue_style('ajtodocss');
	wp_enqueue_style('ajtoskincss');
}
