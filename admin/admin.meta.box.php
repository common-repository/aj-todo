<?php
add_action('wp_ajax_ajtodo_meta_box_ajax', 'ajtodo_meta_box_ajax' );
add_action('wp_ajax_nopriv_ajtodo_meta_box_ajax', 'ajtodo_meta_box_ajax' );

add_action('wp_ajax_ajtodo_team_todo_ajax', 'ajtodo_team_todo_ajax' );
add_action('wp_ajax_nopriv_ajtodo_team_todo_ajax', 'ajtodo_team_todo_ajax' );

add_filter('the_content', 'ajtodo_linkview');
function ajtodo_linkview($content){
	global $post;
	$notice = "";
	$posttype = "ajtododoc";
	$content = ajtodo_setpkeycontent($content);
	if(is_single() && (get_post_type($post->ID) == $posttype)){
		$ajsaved = get_post_meta($post->ID, 'ajtodo_mt_linkdata');
		if($ajsaved){
			$notice = "<div class='ajtodo' style='margin-bottom:8px;'>";
			$linkinfo = new AJTODO_ProjectLink();
			$linkid = $linkinfo->getLinkID($posttype, get_the_ID());
			$ajtodo_mt_linktype = $ajsaved[0]["ajtodo_mt_linktype"];
			$ajtodo_mt_project = $ajsaved[0]["ajtodo_mt_project"];
			$ajtodo_mt_plan = $ajsaved[0]["ajtodo_mt_plan"];
			$ajtodo_mt_todo = $ajsaved[0]["ajtodo_mt_todo"];

			$prj = new AJTODO_Project();
			$prj->id = $ajtodo_mt_project;
			$prj->setProject();
			
			if($ajtodo_mt_todo){
				$todo = new AJTODO_Todo();
				$todo->project = $prj;
				$todo->id = $ajtodo_mt_todo;
				$notice .= $todo->getTodoView();
			}else if($ajtodo_mt_plan){
				$notice .= $prj->getReportSummary($ajtodo_mt_plan, true);
			}else if($ajtodo_mt_project){
				$notice .= $prj->getReportSummary("", true);
			}
			$notice .= "</div>";

			return $notice.$content;
		}else{
			return $content;
		}
	}
    return $content;
}
function ajtodo_setpkeycontent($content){
	global $post;
	$posttype = "ajtododoc";
	if(is_single() && (get_post_type($post->ID) == $posttype)){
		preg_match_all('/\[([A-Z]+-[0-9]+)\]/', $content, $matches);
		if(count($matches)){
			$todo = new AJTODO_Todo();
			foreach($matches[0] as $tkey){
				$todoitem = $todo->getTodoViewInlineByTKey($tkey);
				if($todoitem){
					$content = str_replace($tkey, $todoitem, $content);
				}
			}
		}
	}
    return $content;
}
function ajtodo_add_custom_box()
{
	add_meta_box(
		'ajtodo_link_doc',
		__('프로젝트 링크',"ajtodo"),
		'ajtodo_showselecteditem',
		'ajtododoc',
		'side',
		'high'
	);
}
add_action('add_meta_boxes', 'ajtodo_add_custom_box');
function ajtodo_showselecteditem($post)
{
	global $wpdb;
	$posttype = "ajtododoc";

	$ajtodo_mt_linktype = ajtodo_get("tid", "") ? "todo" : "plan";
	$ajtodo_mt_project = ajtodo_get("pid", "");
	$ajtodo_mt_plan = ajtodo_get("planid", "");
	$ajtodo_mt_todo = ajtodo_get("tid", "");
	$linkid = "";

	$ajsaved = get_post_meta( get_the_ID(), 'ajtodo_mt_linkdata');
	if($ajsaved){
		$linkif = new AJTODO_ProjectLink();
		$linkInfo = $linkif->getLink($posttype, get_the_ID());
		$linkid = $linkInfo->id;
		$ajtodo_mt_project = $linkInfo->projectid;
		$ajtodo_mt_linktype = $ajsaved[0]["ajtodo_mt_linktype"];
		$ajtodo_mt_plan = $ajsaved[0]["ajtodo_mt_plan"];
		$ajtodo_mt_todo = $ajsaved[0]["ajtodo_mt_todo"];
	}

	$plist = new AJTODO_ProjectList();
	$plist->getAllProjects();
    wp_enqueue_script('ajtodo_meta_box_js', AJTODO_JS_PATH."ajtodo_meta_box.".AJTODO_JSMIN."js", array('jquery'), '1.0', true );
	wp_localize_script('ajtodo_meta_box_js', 'ajax_mb_info', array(
        'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajtodo-ajax-nonce'),
		'ptype' => $ajtodo_mt_linktype,
		'pid' => $ajtodo_mt_project,
		'planid' => $ajtodo_mt_plan,
		'todoid' => $ajtodo_mt_todo,
	));
	$prj_d = wp_get_object_terms($post->ID, 'project');
    ?>
	<div>
		<input type="hidden" name="ajtodo_mt_linkid" value="<?php echo $linkid;?>">
		<input type="hidden" name="ajtodo_mt_project" value="<?php echo $ajtodo_mt_project;?>">
		<p for="wporg_field" style='margin:4px 0px;'><?php echo __("링크 타입","ajtodo")?></p>
		<select name="ajtodo_mt_linktype" id="ajtodo_mt_linktype" 
			style='padding:0px 4px;margin-bottom: 8px; width:100%;box-sizing:border-box;'>
			<option value="" <?php if($ajtodo_mt_linktype == ""){ echo "selected"; }?>><?php echo __("현재 프로젝트","ajtodo")?></option>
			<option value="plan" <?php if($ajtodo_mt_linktype == "plan"){ echo "selected"; }?>><?php echo __("플랜","ajtodo")?></option>
			<option value="todo" <?php if($ajtodo_mt_linktype == "todo"){ echo "selected"; }?>><?php echo __("할일","ajtodo")?></option>
		</select>
	</div>
	<div id='ajtodo_forplan' <?php if($ajtodo_mt_linktype == "") { echo "style='display:none;'"; }?>>
		<p for="wporg_field" style='margin:4px 0px;'><?php echo __("플랜 선택","ajtodo")?></p>
		<select name="ajtodo_mt_plan" id="ajtodo_mt_plan"
			style='padding:0px 4px;margin-bottom: 8px;width:100%;box-sizing:border-box;'>
		<?php
			$sql = "select id, plantitle from ".AJTODO_DB_PLAN." where projectid = ".$ajtodo_mt_project;
			$list = $wpdb->get_results($sql, ARRAY_A);
			$list[] = array("id" => "0", "plantitle" => __("플랜 없음","ajtodo"));
			foreach($list as $p){
				echo "<option value='".$p["id"]."' ";
				if($ajtodo_mt_plan == $p["id"]){
					echo " selected ";
				}
				echo " >".$p["plantitle"]."</option>";
			}
		?>
		</select>
	</div>
	<div id='ajtodo_fortodo' <?php if(!($ajtodo_mt_linktype == "todo")) { echo "style='display:none;'"; }?>>
		<p for="wporg_field" style='margin:4px 0px;'><?php echo __("할일 선택","ajtodo")?></p>
		<select name="ajtodo_mt_todo" id="ajtodo_mt_todo"
			style='padding:0px 4px;margin-bottom: 8px;width:100%;box-sizing:border-box;'>
		<?php
		if($ajtodo_mt_linktype == "todo"){
			$sql = "select id, title from ".AJTODO_DB_TODO."_".$ajtodo_mt_project." where projectid = ".$ajtodo_mt_project;
			$sql .= " and planid = ".$ajtodo_mt_plan;
			$list = $wpdb->get_results($sql, ARRAY_A);
			foreach($list as $p){
				echo "<option value='".$p["id"]."' ";
				if($ajtodo_mt_todo == $p["id"]){
					echo " selected ";
				}
				echo " >".$p["title"]."</option>";
			}
		}
		?>
		</select>
	</div>
    <?php
}
function ajtodo_save_postdata($post_id)
{
	if(!ajtodo_post("ajtodo_mt_project", ""))
		return;

	$posttype = "ajtododoc";
	$ajtodo_mt_linkid = ajtodo_post("ajtodo_mt_linkid", "");
	$ajtodo_mt_linktype = ajtodo_post("ajtodo_mt_linktype", "");
	$ajtodo_mt_linkdata = array(
		"ajtodo_mt_linktype" => $ajtodo_mt_linktype,
		"ajtodo_mt_project" => ajtodo_post("ajtodo_mt_project", ""),
		"ajtodo_mt_plan" => ajtodo_post("ajtodo_mt_plan", ""),
		"ajtodo_mt_todo" => ajtodo_post("ajtodo_mt_todo", "")
	);
	if($ajtodo_mt_linktype == "plan"){
		$ajtodo_mt_linkdata["ajtodo_mt_todo"] = "";
	}else if($ajtodo_mt_linktype == ""){
		$ajtodo_mt_linkdata["ajtodo_mt_plan"] = "";
		$ajtodo_mt_linkdata["ajtodo_mt_todo"] = "";
	}
	$prj = new AJTODO_Project();
	$prj->id = $ajtodo_mt_linkdata["ajtodo_mt_project"];
	$prj->setProject();
	wp_set_object_terms( $post_id, $prj->pkey, 'project' );
	update_post_meta($post_id, 'ajtodo_pid', $prj->id);
	update_post_meta($post_id, 'ajtodo_mt_linkdata', $ajtodo_mt_linkdata);
	$linkinfo = new AJTODO_ProjectLink();
	$linkinfo->updateLinkInfo(
		$ajtodo_mt_linkid,
		$ajtodo_mt_linkdata["ajtodo_mt_project"],
		$ajtodo_mt_linkdata["ajtodo_mt_plan"],
		$ajtodo_mt_linkdata["ajtodo_mt_todo"],
		$posttype, $post_id);
}
add_action('save_post_ajtododoc', 'ajtodo_save_postdata', 100, 1);
add_action('edit_post_ajtododoc', 'ajtodo_save_postdata', 100, 1);
add_action('rest_insert_ajtododoc', function($post){
	ajtodo_save_postdata($post->ID);
}, 10, 2 );

function ajtodo_reponse_api_post($res) {
	global $wpdb;
	if($res->data["status"] == "publish"){
		$prj_d = get_post_meta($res->data["id"], 'ajtodo_pid', true);
		if($prj_d){
			$sql = "SELECT pkey FROM ".AJTODO_DB_PROJECT." where id = ".$prj_d;
			$pkey = $wpdb->get_var($sql);
			if($pkey){
				$res->data["link"] = str_replace('%project%', $pkey, $res->data["link"]);
			}
		}
	}else{
		$pid = ajtodo_get("pid","");
		if($pid){
			update_post_meta($res->data["id"], 'ajtodo_pid', $pid);
		}
	}
	return $res;
}
add_filter('rest_prepare_ajtododoc', 'ajtodo_reponse_api_post', 10, 1 );

function ajtodo_meta_box_ajax(){
	$type = ajtodo_post("type", "");
	$pid = ajtodo_post("pid", "");
	$planid = ajtodo_post("planid", "0");
	global $wpdb;
	switch($type){
		case "getplans":
			$sql = "select id, plantitle from ".AJTODO_DB_PLAN." where projectid = ".$pid;
			$list = $wpdb->get_results($sql);
			$list[] = array("id" => "0", "plantitle" => __("플랜 없음","ajtodo"));
			wp_send_json(json_encode($list));
			break;
		case "gettodo":
			$sql = "select id, title from ".AJTODO_DB_TODO."_".$pid." where projectid = ".$pid;
			$sql .= " and planid = ".$planid;
			wp_send_json(json_encode($wpdb->get_results($sql)));
			break;
	}
}
