<?php
class AJTODO_ProjectLink{
	public $project;
	public $msg = "";

	public function __construct(){
	}

	public function deleteLink($posttype, $postid){
		global $wpdb;
		if($posttype && $postid){
			$sql = "delete from ".AJTODO_DB_LINK." where posttype = '".$posttype."' and postid = ".$postid;
			$wpdb->query($sql);

			do_action('ajtodo_del_doclink', $postid);
		}
	}

	public function getDocs($projectid, $planid, $todoid){
		global $wpdb;
		$docs = array();
		$wh = "";
		if($todoid){
			$wh = " where todoid = ".$todoid." and projectid = ".$projectid;
		}else if($planid){
			$wh = " where planid = ".$planid." and projectid = ".$projectid;
		}else{
			$wh = " where projectid = ".$projectid;
		}
		$sql = "select postid from ".AJTODO_DB_LINK.$wh." order by regdate desc";
		$postids = $wpdb->get_results($sql);
		$postids_arr = array();
		foreach($postids as $p){
			$postids_arr[] = $p->postid;
		}
		$postids_arr = array_unique($postids_arr);
		if(count($postids_arr)){
			$args = array(
				'post_type' => 'ajtododoc',
				'post__in' => $postids_arr
			);
			$posts = get_posts($args);
			foreach($posts as $p){
				$docs[] = array('title' => $p->post_title, 'link' => get_permalink($p->ID));
			}
		}

		$docs = apply_filters('ajtodo_get_doclinkposts', $docs, $projectid, $planid, $todoid);

		return $docs;
	}

	public function getLink($posttype, $postid){
		global $wpdb;
		$sql = "select * from ".AJTODO_DB_LINK;
		$sql .= " where posttype = '".$posttype."' and postid = ".$postid;
		$ret = $wpdb->get_row($sql);

		$ret = apply_filters('ajtodo_get_linkinfobypostid', $ret, $posttype, $postid);

		return $ret;
	}

	public function getLinkProjectKey($postid){
		global $wpdb;

		$sql = "SELECT a.pkey FROM ".AJTODO_DB_PROJECT." as a join ".AJTODO_DB_LINK." as b ";
		$sql .= " on a.id = b.projectid and b.postid = ".$postid;
		return $wpdb->get_var($sql);
	}

	public function getLinkID($posttype, $postid){
		global $wpdb;
		$sql = "select id from ".AJTODO_DB_LINK;
		$sql .= " where posttype = '".$posttype."' and postid = ".$postid;
		return $wpdb->get_var($sql);
	}

	public function updateLinkInfo($linkid, $pid, $planid, $todoid, $posttype, $postid){
		global $wpdb;
		$ret = "";
		//$this->deleteLink($posttype, $postid);
		if($wpdb->get_var("select count(*) from ".AJTODO_DB_LINK." where postid = ".$postid)){
			$sql = "update ".AJTODO_DB_LINK." set ";
			$sql .= "planid = ".($planid ? $planid : "NULL") .",";
			$sql .= "todoid = ".($todoid ? $todoid : "NULL");
			$sql .= " where postid = ".$postid;
			$ret = $wpdb->query($sql);

			do_action('ajtodo_modify_doclink', $posttype, $postid);

		}else{
			$sql = "insert into ".AJTODO_DB_LINK."(projectid, planid, todoid, posttype, postid, regdate) values(";
			$sql .= ($pid ? $pid : "NULL") .",";
			$sql .= ($planid ? $planid : "NULL") .",";
			$sql .= ($todoid ? $todoid : "NULL") .",";
			$sql .= "'".$posttype ."',";
			$sql .= $postid .",";
			$sql .= "'".date("Y-m-d H:i:s") ."');";
			$ret = $wpdb->query($sql);

			do_action('ajtodo_add_doclink', $posttype, $postid);
		}
		return $ret;
	}

	public static function unLinkPostId($postid){
		global $wpdb;
		$wpdb->query("delete from ".AJTODO_DB_LINK." where postid = ".$postid);
	}

	public static function updateLinkedTodo($pid, $tid){
		global $wpdb;
		$posts = $wpdb->get_results("select * from ".AJTODO_DB_LINK." where projectid = ".$pid." and todoid = ".$tid);
		foreach($posts as $post){
			$data = array(
				"ajtodo_mt_linktype" => "plan",
				"ajtodo_mt_project" => $pid,
				"ajtodo_mt_plan" => $post->planid,
				"ajtodo_mt_todo" => ""
			);
			update_post_meta($post->postid, 'ajtodo_mt_linkdata', $data);
		}
		$wpdb->query("update ".AJTODO_DB_LINK." set todoid = null where projectid = ".$pid." and todoid = ".$tid);
	}

	public function getLinkedDocsByProject(){
		global $wpdb;
		return $wpdb->get_results("select * from ".AJTODO_DB_LINK." where projectid = ".$this->project->id." order by regdate desc limit 10");
	}

	public function getLinkedDocsByPlan($key){
		global $wpdb;
		return $wpdb->get_results("select * from ".AJTODO_DB_LINK." where planid = ".$key." order by regdate desc limit 10");
	}

	public function getLinkedDocsByTodo($key){
		global $wpdb;
		$sql = "select * from ".AJTODO_DB_LINK." where projectid = ".$this->project->id." and todoid = ".$key." order by regdate desc limit 10";
		return $wpdb->get_results($sql);
	}
}
