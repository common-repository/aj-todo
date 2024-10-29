<?php
class AJTODO_ProjectList{
	private $common;
	private $buttons;
	private $views;
	private $skinPath;
	private $ajtodo_roles;
	private $ajtodo_alert;

	public $plist;

	public function __construct($common = ""){
	}

	public function init($common){
		if(isset($common)){
			$this->common = $common;

			include($this->common->skinPath . "/buttons.php");
			include($this->common->skinPath . "/views.php");
			$this->buttons = $buttons;
			$this->views = $views;

			$this->ajtodo_roles = new AJTODO_Role($this->common);
			$this->ajtodo_roles->setRoles();

			$this->ajtodo_alert = new AJTODO_Alert($this->common);
		}
	}

	public function getAllProjects($onlyactive = ""){
		global $wpdb;
		$user = wp_get_current_user();
		$sql = "SELECT * FROM ".AJTODO_DB_PROJECT." 
			where ( authorid = $user->ID ".($onlyactive ? "and donedate is NULL" : "")." 
			or projecttype is null )
			order by updated desc";
		$this->plist = $wpdb->get_results($sql);

		$this->plist = apply_filters('ajtodo_get_allproject', $this->plist);
	}

	public function getList($onlyactive = ""){
		global $wpdb;
		$user = wp_get_current_user();
		$sql = "SELECT * FROM ".AJTODO_DB_PROJECT." 
			where ( authorid = $user->ID ".($onlyactive ? "and donedate is NULL" : "")." 
			or projecttype is null )
			order by updated desc";
		$this->plist = $wpdb->get_results($sql);
		
		$this->plist = apply_filters('ajtodo_get_allproject', $this->plist);
	}

	public function canCreateProject(){
		return current_user_can("ajtodo_project_private");
	}

	private function getProjectListView($isteam, $isdone){
		$has = false;
		$ret = "";
		if($isteam){
			if($isdone){
				$ret .="<div class='fs18 text-bold'>".__("완료된 팀 프로젝트","ajtodo")."</div>";
			}else{
				$ret .="<div class='fs18 text-bold'>".__("진행중인 팀 프로젝트","ajtodo")."</div>";
			}
		}else{
			if($isdone){
				$ret .="<div class='fs18 text-bold'>".__("완료된 개인 프로젝트","ajtodo")."</div>";
			}else{
				$ret .="<div class='fs18 text-bold'>".__("진행중인 개인  프로젝트","ajtodo")."</div>";
			}
		}
		$ret .= "<div class='ajtodo_projectlist'>
			<table class='table table-hover ".($isdone ? " done " : "")."'>
				<thead>
					<tr>
						<th style='width:100px;'>".__("프로젝트 키","ajtodo")."</th>
						<th style='width:250px;'>".__("프로젝트 이름","ajtodo")."</th>
						<th style='width:300px;'>".__("진행상황","ajtodo")."</th>
						<th style='width:10%;min-width:100px' class='text-center'>".__("완료","ajtodo")."/".__("전체","ajtodo")."</th>
						<th class=''>".($isteam ? __("멤버","ajtodo") : "")."</th>
					</tr>
				</thead>
			<tbody>";
		foreach($this->plist as $p){
			if((!$isteam && $p->projecttype == "team") || ($isteam && $p->projecttype == "private")) { continue; }
			if((!$isdone && $p->donedate) || ($isdone && !$p->donedate)) { continue; }

			$has = true;
			$prj = new AJTODO_Project($p);
			$prj->init($this->common);
			$ret .= $prj->getView();
		}
		$ret .= "</tbody></table></div>";
		return $has ? $ret : "";
	}

	public function getView(){
		$hasDoneProject = false;
		$ret = "";
		if(current_user_can("ajtodo_project_team_viewlist")){
			$ret .= $this->getProjectListView(true, false);
			$ret .= $this->getProjectListView(true, true);
		}
		$ret .= $this->getProjectListView(false, false);
		$ret .= $this->getProjectListView(false, true);
		return $ret;
	}

	public function getButton($rolename){
		if($this->ajtodo_roles->roles[$rolename]){
			return $this->buttons[$rolename];
		}else{
			return "";
		}
	}
}
