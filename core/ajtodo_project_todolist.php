<?php
class AJTODO_ProjectTodoList{
	private $common;
	private $authoinfo;
	private $skinPath;
	private $buttons;
	private $forms;
	private $views;
	private $ret_forms;
	private $ajtodo_roles;
	private $ajtodo_alert;

	public $msg;

	public $project;
	public $noplan = false;
	public $planid = "";
	public $authorid = "";
	public $assignid = "";
	public $statuskey = "";
	public $js_templates = "";
	public $onlyactive = true;
	public $uid;
	public $search;
	public $todolist;
	public $todoData;
	public $todotype;
	public $filter_project = "";
	public $filter_user = "";
	public $filter_status = "";
	public $id = "";
	public $title = "";
	public $comment = "";
	public $statustype = "";
	public $regdate = "";
	public $updated = "";

	public function __construct(){
		$this->ajtodo_roles = new AJTODO_Role();
		$this->ajtodo_roles->setRoles();
	}

	public function init($common){
		if(isset($common)){
			$this->common = $common;

			include($this->common->skinPath . "/buttons.php");
			include($this->common->skinPath . "/views.php");
			include($this->common->skinPath . "/js_templates.php");
			$this->js_templates = $js_templates;
			$this->buttons = $buttons;
			$this->views = $views;

			$this->ajtodo_alert = new AJTODO_Alert($this->common);
		}
	}

	public function getFilter(){
		$ret = "<div class='row' id='ajtodo_top_filter_box' style=''>";
		$ret .= $this->views["TodoListProjectFilter"];
		$ret .= $this->views["TodoListFilter"];
		$ret .= "</div>";
		$ret .= "<div class='' id='ajtodo_top_filter_box_empty' style='display:none;'>";
		$ret .= "<div class='alert alert-warning'>".__("할일이 없네요. 프로젝트에 할일을 등록해보세요.","ajtodo")."</div>";
		$ret .= "</div>";
		return $ret;
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

	public function getCreateTodoBox(){
		$defaultType = $this->getDefaultTodoType();
		$ret = "<div class='ajtodo_add_todo_inline'>
			<div class='input-group'>
				<div class='input-group-append' id='ajtodo_set_todotype'>
					<button class='btn btn-outline-secondary dropdown-toggle' type='button' 
					data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' 
					style='margin-right:0px;background:".$defaultType["color"]."' 
					id='ajtodo_set_nowtodotype'>".$defaultType["name"]."</button>
					<div class='dropdown-menu' id='ajtodo_set_nowtodotypelist'>";
		foreach($this->project->todotype as $todotype){
			if($defaultType["key"] != $todotype["key"]){
				$ret .= "<a class='fs12 pt8 pb8 dropdown-item seltodotype' 
					style='background:".$todotype["color"]."' 
					href='#' val='".$todotype["key"]."'>".$todotype["name"]."</a>";
			}
		}
		$ret .= "
					</div>
				</div>
				<input type='text' class='form-control form-control-sm' id='ajtodo_todotitle' placeholder='".__("할일을 입력해보세요.","ajtodo")."'>
			</div>
		</div>";
		return $ret;
	}

	public function getView(){
		$ret = "<div>";
		$ret .= "<div class='float-left' id='ajtodo_listview' style='width:100%'>";
		$ret .= "	<ul class='list-group ajultodolist' id='todo_list'></ul>";
		$ret .= "	<p class='fs14 font-weight-bold donevw' style='color:#949292;margin-top:12px !important;margin-bottom:0px !important;'>".__("완료된 일들","ajtodo")."</p>";
		$ret .= "	<ul class='list-group ajultodolist donevw' id='todo_done_list'></ul>";
		$ret .= "</div>";
		$ret .= "<div id='ajtodo_todoview' class='float-left'></div>";
		$ret .= "<div class='clearfix'></div>";
		$ret .= "</div>";
		return $ret;
	}

	public function getUserProgressView(){
		return $this->views["UserProgress"];
	}

	public function getTodoTypes(){
		global $wpdb;
		$ret = array();
		$wh = "";
		$sql = "SELECT todotype, count(*) as cnt FROM ".AJTODO_DB_TODO."_".$this->project->id;
		if($this->planid !== "")
			$sql .= " where planid = ".$this->planid;
		$sql .= " group by todotype";
		$ret["todotypes"] = $wpdb->get_results($sql);
		return $ret;
	}

	public function getUserProgress(){
		global $wpdb;
		$ret = array();
		$wh = "";

		$sql = "SELECT statuskey, count(*) as cnt FROM ".AJTODO_DB_TODO."_".$this->project->id;
		if($this->planid !== "")
			$sql .= " where planid = ".$this->planid;
		$sql .= " group by statuskey";
		$ret["statuses"] = $wpdb->get_results($sql);
		return $ret;
	}

	public function getList(){
		global $wpdb;
		$userid = $this->uid ? $this->uid :  wp_get_current_user()->ID;
		$ret = array();

		$sql = "SELECT a.*, COUNT(b.id) AS docs FROM ".AJTODO_DB_TODO."_".$this->project->id." as a ";
		$sql .= " left JOIN ".AJTODO_DB_LINK." as b ";
		$sql .= "on a.id = b.todoid and a.projectid = b.projectid where 0 = 0 ";

		if($this->statuskey != ""){
			$sql .= " and a.statuskey = '".$this->statuskey."' ";
		}

		if($this->planid != "")
			$sql .= " and a.planid = ".$this->planid;

		if($this->authorid)
			$sql .= " and a.authorid = $this->authorid ";

		if($this->assignid)
			$sql .= " and a.assignid = $this->assignid ";

		if($this->todotype)
			$sql .= " and a.todotype = '".$this->todotype."' ";

		if($this->search)
			$sql .= " and a.title like '%$this->search%' ";

		$sql .= " group by a.tkey ";
		$sql .= " order by a.donedated, a.updated desc, a.regdate desc";
		$this->todolist = $wpdb->get_results($sql);

		$this->todolist = apply_filters('ajtodo_get_todolist', $this->todolist, $this->project->id);

		$ret["result"] = true;
		$ret["todolist"] = $this->todolist;

		return $ret;
	}

	public function getForm($viewname){
		return $this->views[$viewname];
	}

}

