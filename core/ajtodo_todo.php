<?php
class AJTODO_Todo{
	private $common;
	private $authoinfo;
	private $skinPath;
	private $buttons;
	private $views;
	private $ret_forms;
	private $ajtodo_roles;
	private $ajtodo_alert;

	public $msg;

	public $project;
	public $project_filter = "";
	public $js_templates = "";
	public $tkey = "";
	public $plan = "";
	public $tData;
	public $pData;
	public $id = "";
	public $projectid = "";
	public $todotype = "";
	public $status = "";
	public $category = "";
	public $title = "";
	public $comment = "";
	public $projecttype = "";
	public $projectstatus = "";

	public function __construct($prj = ""){
		if($prj){
			$this->pData = $prj;
			$this->pData->authorname = get_user_by('id', $this->pData->authorid)->display_name;
			if($this->pData->totalissue > 0){
				$this->pData->percent = $this->pData->doneissue * 100 / $this->pData->totalissue;
			}else{
				$this->pData->percent = 0;
			}
		}
	}

	public function init($common){
		$this->common = $common;
		include($this->common->skinPath . "/buttons.php");
		include($this->common->skinPath . "/views.php");
		include($this->common->skinPath . "/js_templates.php");
		$this->js_templates = $js_templates;
		$this->buttons = $buttons;
		$this->views = $views;

		$this->ajtodo_roles = new AJTODO_Role($this->common);
		$this->ajtodo_roles->setRoles();

		$this->ajtodo_alert = new AJTODO_Alert($this->common);
	}

	public function setProject($tkey){
		global $table_prefix, $wpdb;
		$tkey = str_replace("[","",str_replace("]","",$tkey));
		$tmparr = explode("-", $tkey);
		$pkey = $tmparr[0];
		$sql = "SELECT * FROM ".AJTODO_DB_PROJECT." where pkey = '".$pkey."'";
		$pinfo = $wpdb->get_row("SELECT * FROM ".AJTODO_DB_PROJECT." where pkey = '".$pkey."'");
		if(!$pinfo)
			return "";

		$this->project = new AJTODO_Project();
		$this->project->id = $pinfo->id;
		$this->project->setProject();

		$this->tData = $wpdb->get_row("select * from ".AJTODO_DB_TODO."_".$pinfo->id." where tkey = '".$tkey."'");
		if($this->tData){
			$ss = new AJTODO_ProjectTodoType();
			$ss->project = $this->project;
			$this->todotype = $ss->getSingleTodoType($this->tData->todotype);

			$ss = new AJTODO_ProjectStatus();
			$ss->project = $this->project;
			$this->status = $ss->getSingleStatus($this->tData->statuskey);

			if($this->tData->categorykey){
				$ct = new AJTODO_ProjectCategory();
				$ct->project = $this->project;
				$this->category = $ct->getSingleCategory($this->tData->categorykey);
			}

			if($this->tData->planid){
				$pl = new AJTODO_ProjectPlan();
				$this->plan = $pl->getPlan($this->tData->planid);
			}
		}
	}

	public function getTodoViewInlineByTKey($tkey){
		global $table_prefix, $wpdb;
		$tkey = str_replace("[","",str_replace("]","",$tkey));
		$tmparr = explode("-", $tkey);
		$pkey = $tmparr[0];
		$sql = "SELECT * FROM ".AJTODO_DB_PROJECT." where pkey = '".$pkey."'";
		$pinfo = $wpdb->get_row("SELECT * FROM ".AJTODO_DB_PROJECT." where pkey = '".$pkey."'");
		if(!$pinfo)
			return "";

		$this->project = new AJTODO_Project();
		$this->project->id = $pinfo->id;
		$this->project->setProject();

		$this->tData = $wpdb->get_row("select * from ".AJTODO_DB_TODO."_".$pinfo->id." where tkey = '".$tkey."'");
		if($this->tData){
			$ss = new AJTODO_ProjectTodoType();
			$ss->project = $this->project;
			$todotype = $ss->getSingleTodoType($this->tData->todotype);

			$ss = new AJTODO_ProjectStatus();
			$ss->project = $this->project;
			$status = $ss->getSingleStatus($this->tData->statuskey);

			$ret = "<span class='ajtodo' style='border:1px solid #e0e0e0;padding:2px 4px;border-left : 3px solid ".$todotype["color"].";'>";
			$ret .= "<span class='todo_list_item list-group-item-action' status='".$status["key"]."'>";
			$ret .= "<a href='/aj-todo/".$this->tData->tkey."' class='todo_title ajtodo_todo_popdetail' style='cursor:pointer' pid=".$pinfo->id." tid=".$this->tData->id.">";
			$ret .= "<i class='quickdo far ".$status["icon"]."'></i>";
			$ret .= "<span class='badge'>".$this->tData->tkey."</span>";
			$ret .= $this->tData->title;
			if($this->tData->comment)
				$ret .= "<i class='far fa-file-alt' style='margin-left:4px;'></i>";
			$ret .= "</a>";
			$ret .= "</span>";
			$ret .= "</span>";
		}
		return $ret;
	}

	public function getTodoView($inline = true){
		global $table_prefix, $wpdb;
		$sql = "SELECT * FROM ".AJTODO_DB_TODO."_".$this->project->id." where id = ".$this->id;
		$this->tData = $wpdb->get_row($sql);
		$ret = "";
		if($this->tData){
			$ss = new AJTODO_ProjectStatus();
			$ss->project = $this->project;
			$status = $ss->getSingleStatus($this->tData->statuskey);

			$ss = new AJTODO_ProjectTodoType();
			$ss->project = $this->project;
			$todotype = $ss->getSingleTodoType($this->tData->todotype);

			$ret .= "<div id='ajtodo_listview'>";
			$ret .= "<ul class='list-group ajultodolist' id='todo_list'>";
			$ret .= "<li class='todo_list_item list-group-item-action list-group-item' status='".$status["key"]."' style='border-left : 3px solid ".$todotype["color"].";'>";
			$ret .= "<a href='/aj-todo/".$this->tData->tkey."' class='todo_title ajtodo_todo_popdetail' style='cursor:pointer' pid=".$this->project->id." tid=".$this->tData->id.">";
			$ret .= "<i class='quickdo far ".$status["icon"]."'></i>";
			$ret .= "<span class='badge'>".$this->tData->tkey."</span>";
			$ret .= $this->tData->title;
			if($this->tData->comment)
				$ret .= "<i class='far fa-file-alt' style='margin-left:4px;'></i>";
			if($this->tData->categorykey){
				$ct = new AJTODO_ProjectCategory();
				$ct->project = $this->project;
				$category = $ct->getSingleCategory($this->tData->categorykey);
				$ret .= "<kbd style='margin-left:8px;font-size:12px;background:#61a4cc'>";
				$ret .= $category["name"];
				$ret .= "</kbd>";
			}
			$ret .= "</a>";
			if($this->tData->assignid){
				$ass = get_userdata($this->tData->assignid);
				$ret .= "<div class='clearfix float-right inline_assignee'>";
				$ret .= str_replace("<img ", "<img class='rounded-circle' title='".$ass->display_name."' ", get_avatar($ass->ID, 28));
				$ret .= "</div>";
			}
			$ret .= "</li>";
			$ret .= "</ul>";
			$ret .= "</div>";
		}
		return $ret;
	}
	
	public static function getTodo($pid, $tid){
		global $table_prefix, $wpdb;
		$sql = "SELECT * FROM ".AJTODO_DB_TODO."_".$pid." where id = ".$tid;
		return $wpdb->get_row($sql);
	}

	public function getTodoInfo(){
		global $table_prefix, $wpdb;
		$sql = "SELECT a.*, COUNT(b.id) AS docs FROM ".AJTODO_DB_TODO."_".$this->projectid." as a ";
		$sql .= " left JOIN ".AJTODO_DB_LINK." as b ";
		$sql .= "on a.id = b.todoid and a.projectid = b.projectid where a.id = ".$this->id;
		$this->tData = $wpdb->get_row($sql);

		$this->tData = apply_filters('ajtodo_get_todoinfo', $this->tData, $this->projectid, $this->id); 

		if($this->tData){
			return true;
		}else{
			$this->msg = __("정보를 가져올 수 없습니다.","ajtodo");
			return false;
		}
	}

	private function getStatusId($status){
		global $table_prefix, $wpdb;
		$res = $wpdb->get_row("select id from ".AJTODO_DB_TODOSTATUS." where statustype ='".$status."' and isbuiltin = 'Y'");
		return $res->id;
	}

	public function updateStatus($status_id){
		global $table_prefix, $wpdb;
		if($this->ajtodo_roles->roles["UpdateStatusTodo"]){
			$user = wp_get_current_user();
			$status = $wpdb->get_row("select statustype from ".AJTODO_DB_TODOSTATUS. " where id = ".$status_id);
			$sql = "update ".AJTODO_DB_TODO."_".$this->project->id." set updated = '".date("Y-m-d H:i:s")."', ";
			if($status->statustype == "D"){
				$sql .= " statustypeid = ".$status_id.", donedated = '".date("Y-m-d H:i:s")."' ";
			}else{
				$sql .= " statustypeid = ".$status_id.", donedated = NULL ";
			}
			$sql .= " where id =".$this->id;
			$result = $wpdb->query($sql);
			if($result){
				$this->msg = __("변경되었습니다.","ajtodo");
				return true;
			}else{
				$this->msg = __("업데이트 중에 문제가 발생했습니다.","ajtodo");
				return false;
			}
		}else{
			$this->msg = __("권한이 없습니다.","ajtodo");
			return false;
		}
	}

	private function valSet($val){
		$val = $val == ":_NULL_:" ? "" : $val;
		$val = $val == ":_NOW_:" ? date("Y-m-d H:i:s") : $val;
		return $val;
	}

	public function updateTodo($col, $val, $colval){
		global $table_prefix, $wpdb;
		$sql = "update ".AJTODO_DB_TODO."_".$this->project->id." set updated = '".date("Y-m-d H:i:s")."', ";
		if($colval){
			$colval = stripslashes($colval);
			$ar = array();
			foreach(json_decode($colval) as $col => $val){
				$val = $this->valSet($val);
				if($val){
					array_push($ar, "$col = '$val' ");
				}else{
					array_push($ar, "$col = NULL ");
				}
			}
			$sql .= implode(",", $ar);
		}else{
			$val = $this->valSet($val);
			if($val){
				$sql .= "$col = '$val' ";
			}else{
				$sql .= "$col = NULL ";
			}
		}
		$sql .= " where id =".$this->id;
		$result = $wpdb->query($sql);
		if($result){

			do_action('ajtodo_modify_todo', $this->project->id, $this->id);

			$this->msg = __("변경되었습니다.","ajtodo");
			return true;
		}else{
			$this->msg = __("업데이트 중에 문제가 발생했습니다.","ajtodo");
			return false;
		}
	}

	public function createTodo(){
		global $wpdb;
		if($this->project->hasPerm("tp_todo_create")){
			$status = AJTODO_ProjectStatus::getStatusByType($this->project, "S");
			$todotype = $this->todotype ? $this->todotype : $this->getDefaultTodoType()["key"];
			$newtkey = $this->project->getNewTodoIndex();
			$user = wp_get_current_user();
			$authorid = "NULL";
			if($this->project->projecttype == "private"){
				$authorid = $user->ID;
			}else{
				if($this->project->autoassign == "Y"){
					$authorid = $user->ID;
				}
			}
			$sql = "insert into ".AJTODO_DB_TODO."_".$this->project->id."(
				tkey,
				projectid,
				statuskey,
				categorykey,
				authorid, 
				assignid,
				todotype,
				title, 
				planid, 
				comment, 
				updated, 
				regdate) values(
					'".$this->project->pkey."-".$newtkey."',
					".$this->project->id.",
					'".$status["key"]."',
					NULL,
					".$user->ID.",
					".$authorid.",
					'".$todotype."',
					'".$this->title."',
					".($this->planid ? $this->planid : "0").",
					'".$this->comment."',
					'".date("Y-m-d H:i:s")."',
					'".date("Y-m-d H:i:s")."'
				)";
			$result = $wpdb->query($sql);
			if($result){
				$this->project->setNewTodoIndex($newtkey);
				$this->id = $wpdb->insert_id;
				
				do_action('ajtodo_add_todo', $this->project->id, $this->id);

				$this->msg = __("할일이 추가되었습니다.","ajtodo");
				return true;
			}else{
				$this->msg = __("생성 도중에 문제가 발생했습니다.","ajtodo");
				return false;
			}
		}else{
			$this->msg = __("권한이 없습니다.","ajtodo");
			return false;
		}
	}

	public function getDefaultTodoType(){
		$ret = "";
		foreach($this->project->todotype as $r){
			if($r["default"] == "Y"){
				$ret = $r;
			}
		}

		$ret = apply_filters('ajtodo_get_defaulttodotype', $ret, $this->project->id);

		return $ret;
	}

	public function delTodo(){
		global $table_prefix, $wpdb;
		if($this->ajtodo_roles->roles["DelTodo"]){
			$sql = "delete from ".AJTODO_DB_TODO."_".$this->projectid." where id = ".$this->id;
			$result = $wpdb->query($sql);
			if($result){
				
				do_action('ajtodo_del_todo', $this->projectid, $this->id);

				$this->msg = __("삭제되었습니다.","ajtodo");
				return true;
			}else{
				$this->msg = __("삭제 진행중에 오류가 발생했습니다.","ajtodo");
				return false;
			}
		}else{
			$this->msg = __("권한이 없습니다.","ajtodo");
			return false;
		}
	}

	public function getView($viewname){
		if($this->ajtodo_roles->roles[$viewname]){
			return $this->views[$viewname];
		}else{
			return $this->ajtodo_alert->getHtml("notallowed");
		}
	}

	public function getViewCreateTodo(){
	}

}

