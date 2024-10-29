<?php
class AJTODO_TodoList{
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
	public $js_templates = "";
	public $onlyactive = true;
	public $uid;
	public $search;
	public $todolist;
	public $planid;
	public $todoData;
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
		$ret = "<div class='row' id='ajtodo_top_filter_box>";
		$ret .= $this->views["TodoListProjectFilter"];
		$ret .= $this->views["TodoListFilter"];
		$ret .= "</div>";
		return $ret;
	}

	public function getView(){
		$ret = $this->views["TodoListStart"];
		$ret .= $this->views["TodoListEnd"];
		$ret .= $this->views["TodoListDoneStart"];
		$ret .= $this->views["TodoListDoneEnd"];
		return $ret;
	}

	public function getUserProgressView(){
		return $this->views["UserProgress"];
	}

	public function getUserProgress(){
		global $wpdb;
		$openstatus = AJTODO_ProjectStatus::getStatusByType($this->project, "S");
		$userid = $this->uid ? $this->uid :  wp_get_current_user()->ID;
		$sql = "SELECT count(id) FROM ".AJTODO_DB_TODO."_".$this->project->id." where 0 = 0 ";
		if($this->planid)
			$sql .= " and planid = ".$this->planid;
		
		$ret = array();
		$ret["totalcount"] = $wpdb->get_var($sql);
		$ret["opencount"] = $wpdb->get_var($sql." and statuskey = '".$openstatus["key"]."'");
		$ret["donecount"] = $wpdb->get_var($sql." and donedated is not null ");
		return $ret;
	}

	public function getList(){
		global $wpdb;
		$ret = array();
		$sql = "SELECT * FROM ".AJTODO_DB_TODO."_".$this->project->id;
		$sql .= " order by donedated, updated desc, regdate desc";
		$this->todolist = $wpdb->get_results($sql);
		$ret["result"] = true;
		$ret["todolist"] = $this->todolist;
		return $ret;
	}

	public function getForm($viewname){
		return $this->views[$viewname];
	}

}

