<?php
class AJTODO_Project{
	private $common;
	private $authoinfo;
	private $skinPath;
	private $buttons;
	private $views;
	private $ret_forms;
	private $ajtodo_roles;
	private $ajtodo_alert;
	
	public $msg = "";
	public $pData;
	public $percent = 0;
	public $id = "";
	public $pkey = "";
	public $title = "";
	public $comment = "";
	public $plan = array();
	public $todoperms = array();
	public $planperms = array();
	public $docperms = array();
	public $currentuserperms = array();
	public $currentuserstatus = array();
	public $statuses= array();
	public $roles= array();
	public $roleperm = array();
	public $category = array();
	public $todotype = array();
	public $projecttype = "private";
	public $projectstatus = "";
	public $authorname = "";
	public $autoassign = "N";
	public $doneissue = "";
	public $totalissue = "";
	public $updated = "";
	public $regdate = "";
	public $donedate = "";

	public function __construct($prj = ""){
		if($prj){
			$this->id = $prj->id;
			$this->pkey = $prj->pkey;
			$this->title = $prj->title;
			$this->comment = $prj->comment;
			$this->authorid = $prj->authorid;
			$this->roles = $prj->roles;
			$this->statuses = $prj->statuses;
			$this->roleperm = $prj->roleperm;
			$this->category = $prj->category;
			$this->todotype = $prj->todotype;
			$this->autoassign = $prj->autoassign;
			$this->projecttype = $prj->projecttype;
			$this->projectstatus = $prj->projectstatus;
			$this->statuses= $prj->statuses;
			$this->updated = $prj->updated;
			$this->regdate = $prj->regdate;
			$this->authorname = get_user_by('id', $this->authorid)->display_name;
		}
	}

	public function init($common){
		$this->common = $common;
		include($this->common->skinPath . "/buttons.php");
		include($this->common->skinPath . "/views.php");
		$this->buttons = $buttons;
		$this->views = $views;

		$this->ajtodo_roles = new AJTODO_Role($this->common);
		$this->ajtodo_roles->setRoles();

		$this->ajtodo_alert = new AJTODO_Alert($this->common);
	}

	private function getCreateProjectForm($rolename){
	}

	public static function getProjectKeyById($pid){
		global $wpdb;
		$obj = $wpdb->get_row("select pkey from ".AJTODO_DB_PROJECT." where id = ".$pid);
		return $obj ? $obj->pkey : "";
	}

	public function setProject(){
		global $wpdb;
		if($this->id){
			$obj = $wpdb->get_row("select * from ".AJTODO_DB_PROJECT." where id = ".$this->id);	
			$this->pkey = $obj->pkey;
			$this->title = $obj->title;
			$this->comment = $obj->comment;
			$this->projecttype = $obj->projecttype;
			$this->authorid = $obj->authorid;
			$this->statuses= $obj->roles ? json_decode($obj->statuses, true) : array();
			$this->roles= $obj->roles ? json_decode($obj->roles, true) : array();
			$this->roleperm = $obj->roleperm ? json_decode($obj->roleperm, true) : array();
			$this->statuses= $obj->statuses ? json_decode($obj->statuses, true) : array();
			$this->category = $obj->category ? json_decode($obj->category, true) : array();
			$this->todotype = $obj->todotype ? json_decode($obj->todotype, true) : array();
			$this->autoassign = $obj->autoassign;
			$this->updated = $obj->updated;
			$this->regdate = $obj->regdate;
			$this->donedate = $obj->donedate;
			$this->currentuserperms = AJTODO_User::getPerms($this);
			$this->currentuserstatus = AJTODO_User::getStatusRole($this);
			$this->setPlanPerms();
			$this->setTodoPerms();
			$this->setDocPerms();
			$this->setPlan();
		}
	}
	
	public static function getProject($pid){
		global $wpdb;
		if($pid){
			return $wpdb->get_row("select * from ".AJTODO_DB_PROJECT." where id = ".$pid);
		}
		return "";
	}

	private function setPlan(){
		global $table_prefix, $wpdb;
		$sql = "select *, IF(ISNULL(donedate), startdate, donedate) as a1 ";
		$sql .= " from ".AJTODO_DB_PLAN." where projectid = ".$this->id;
		$sql .= " order by a1";
		$this->plan = $wpdb->get_results($sql, ARRAY_A);
	}

	private function setPlanPerms(){
		foreach($this->currentuserperms as $pem){
			if(strstr($pem, "tp_plan_"))
				$this->planperms[] = $pem;
		}
	}

	private function setDocPerms(){
		foreach($this->currentuserperms as $pem){
			if(strstr($pem, "tp_doc_"))
				$this->docperms[] = $pem;
		}
	}

	private function setTodoPerms(){
		foreach($this->currentuserperms as $pem){
			if(strstr($pem, "tp_todo_"))
				$this->todoperms[] = $pem;
		}
	}

	public function hasPerm($perm){
		return in_array($perm, $this->currentuserperms);
	}

	public function createForm(){
		global $wpdb;
		include(AJTODO_PLUGIN_PATH . "inc/ajtodo_js.php");
		$retform = "";
		if(current_user_can("ajtodo_project_private") || current_user_can("ajtodo_project_team_create")){
			$retform .= $this->setProject();
			$retform .= $this->getForm_PKey();
			$retform .= $this->getForm_Title();
			$retform .= $this->getForm_Projecttype();
			$retform .= $this->getForm_AutoAssign();
			$retform .= $this->getForm_Comment();

			$retform = apply_filters('ajtodo_add_projectform_after', $retform);

			$retform .= $this->getForm_Submits();
		}else{
			$retform .= $this->getForm_Error(__("권한이 없습니다.","ajtodo"));
		}

		$ret = "<form class='form' style='max-width:600px;' id='ajtodo_form'>";
		$ret .= $retform;
		$ret .= "</form>";
		return $ret;
	}

	private function getForm_PKey(){
		$ret = "
			<div class='form-group'>
				<label for='ajtodo_pkey'>".__("프로젝트 고유키","ajtodo")."</label>";
		if($this->id){
			$ret .= "<p class='font-italic'>".$this->pkey."</p>";
			$ret .= "<input type='hidden' name='pkey' id='ajtodo_pkey' value='".$this->pkey."'>";
		}else{
			$ret .= "<input type='text' 
						class='form-control form-control-sm'
						name='pkey' maxlength = 5
						id='ajtodo_pkey' style='width:150px;'
						placeholder='".__("프로젝트 고유키","ajtodo")."' 
						value='".($this->id ? $this->pkey : "")."'>";
		}
		$ret .= "</div>";
		return $ret;
	}
	private function getForm_Title(){
		if(!$this->donedate){
			$ret = "
				<div class='form-group'>
					<label for='ajtodo_title'>".__("프로젝트 이름","ajtodo")."</label>
					<input type='text' 
						class='form-control form-control-sm'
						name='title'
						id='ajtodo_title' style='width:300px;'
						placeholder='".__("프로젝트 이름","ajtodo")."' 
						value='".($this->id ? $this->title : "")."'>
				</div>";
		}else{
			$ret = "
				<div class='form-group'>
					<label for='ajtodo_title'>".__("프로젝트 이름","ajtodo")."</label>
					<p class='font-italic fs16'>".$this->title."</p>
					<input type='hidden' 
						class='form-control form-control-sm'
						name='title'
						id='ajtodo_title' style='width:300px;'
						placeholder='".__("프로젝트 이름","ajtodo")."' 
						value='".($this->id ? $this->title : "")."'>
				</div>";
		}
		return $ret;
	}
	private function getForm_Projecttype(){
		$ret = "<div class='form-group'><label for='ajtodo_projecttype'>".__("프로젝트 타입","ajtodo")."</label><div>";
		if(current_user_can("ajtodo_project_private")){
			$ret .= "
					<div class='form-check form-check-inline'>
						<input class='form-check-input' type='radio' 
							name='projecttype' 
							id='ajtodo_projecttype_private' 
							value='private' ".($this->projecttype == "private" ? "checked" :  "")." ".($this->id ? "disabled" : "").">
						<label class='form-check-label' for='ajtodo_projecttype_private'>
						".__("개인 프로젝트","ajtodo")."</label>
					</div>";
		}
		if(current_user_can("ajtodo_project_team_create")){
			$ret .= "
					<div class='form-check form-check-inline' [ROLE_CreateTeamProject_HIDE]>
						<input class='form-check-input' type='radio' 
							name='projecttype' 
							id='ajtodo_projecttype_team' 
							value='team' ".($this->projecttype == "team" || !current_user_can("ajtodo_project_private") ? "checked" :  "")." ".($this->id ? "disabled" : "").">
						<label class='form-check-label' for='ajtodo_projecttype_team'>
						".__("팀 프로젝트","ajtodo")."</label>
					</div>";
		}
		$ret .= "</div></div>";
		return $ret;
	}

	private function getForm_AutoAssign(){
		$isY = true;
		if($this->id){
			$isY = $this->projecttype == "private";
		}
		$ret = "";
		if(!$this->donedate){
			$ret = "
				<div class='form-group' id='aassign' ".($isY ? "style='display:none;'" : "" )."'>
					<label for='ajtodo_title'>".__("자동 담당자 지정","ajtodo")."</label>
					<div>
						<div class='form-check form-check-inline'>
							<input type='checkbox' 
								class='form-check-input'
								name='autoassign'
								id='autoassign' 
								".($this->autoassign == "Y" ? " checked "  : "")."'>
							<span>".__("할일 작성자가 자동으로 담당자가 됩니다","ajtodo")."</span>
						</div>
					</div>
				</div>";
		}
		return $ret;
	}

	private function getForm_Comment(){
		if(!$this->donedate){
			$ret = "
				<div class='form-group'>
					<label for='ajtodo_comment'>".__("프로젝트 설명","ajtodo")."</label>
					<textarea type='text' class='form-control form-control-sm'
						name='comment'
						id='ajtodo_comment' style='width:100%;height:150px;'>".$this->comment."</textarea>
				</div>";
		}else{
			$ret = "
				<div class='form-group'>
					<label for='ajtodo_comment'>".__("프로젝트 설명","ajtodo")."</label>
					<p class='font-italic'>".$this->comment."</p>
					<input type='hidden' class='form-control form-control-sm'
						name='comment'
						id='ajtodo_comment' value='".$this->comment."'>
				</div>";
		}
		return $ret;
	}

	private function getForm_Submits(){
		$ret = "<div class='ajtodo_create_project_submits'>";
		if(!$this->donedate){
			$ret .= "<button class='btn btn-primary' id='btnCreateProject'>".($this->id ? __("프로젝트 수정","ajtodo") : __("프로젝트 생성","ajtodo"))."</button>";

			if($this->id){
				if( $this->projecttype == "private" || 
					(current_user_can("ajtodo_project_team_close") && $this->projecttype == "team") ){
					if($this->donedate){
						$ret .= "<a href='' val='".$this->id."' id='btnActiveProject' class='btn btn-success'>".__("프로젝트 활성화하기","ajtodo")."</a>";
					}else{
						$ret .= "<a href='?page=ajtodo_admin_project&ajtodo_type=done&id=".$this->id."' class='btn btn-success'>".__("프로젝트 완료하기","ajtodo")."</a>";
					}
				}
				if( $this->projecttype == "private" || 
					(current_user_can("ajtodo_project_team_remove") && $this->projecttype == "team") ){
					$ret .= "<a href='?page=ajtodo_admin_project&ajtodo_type=del&id=".$this->id."' class='btn btn-danger' id='btnRemoveProject'>".__("프로젝트 삭제하기","ajtodo")."</a>";
				}
			}
		}else{
			if( $this->projecttype == "private" || 
				(current_user_can("ajtodo_project_team_close") && $this->projecttype == "team") ){
				$ret .= "<a href='' val='".$this->id."' id='btnActiveProject' class='btn btn-success'>".__("프로젝트 활성화하기","ajtodo")."</a>";
			}
			if( $this->projecttype == "private" || 
				(current_user_can("ajtodo_project_team_remove") && $this->projecttype == "team") ){
				$ret .= "<a href='?page=ajtodo_admin_project&ajtodo_type=del&id=".$this->id."' class='btn btn-danger' id='btnRemoveProject'>".__("프로젝트 삭제하기","ajtodo")."</a>";
			}
		}
		$ret .= "</div>";
		return $ret;
	}

	private function getForm_Error($msg){
		return "<div class=''>
			<div class='alert alert-danger' role='alert'>$msg</div>
		</div>";
	}

	private function valSet($val){
		$val = $val == ":_NULL_:" ? "" : $val;
		$val = $val == ":_NOW_:" ? date("Y-m-d H:i:s") : $val;
		return $val;
	}

	public function updateCols($colval){
		global $wpdb;
		$user = wp_get_current_user();
		$ar = array();
		$sql = "update ".AJTODO_DB_PROJECT." set ";
		foreach($colval as $key => $val){
			$val = $this->valSet($val);
			if($val){
				array_push($ar, "$key = '$val' ");
			}else{
				array_push($ar, "$key = NULL ");
			}
		}
		$sql .= implode(",", $ar);
		$sql .= " where id = $this->id";
		if($wpdb->query($sql) === FALSE){

			do_action('ajtodo_modify_project', $this->id);

			return false;
		}else{
			return true;
		}
	}

	public function updateTodo($colval, $wherecol, $whereval){
		global $wpdb;
		$ar = array();
		$sql = "update ".AJTODO_DB_TODO."_".$this->id." set ";
		foreach($colval as $key => $val){
			$val = $this->valSet($val);
			if($val){
				array_push($ar, "$key = '$val' ");
			}else{
				array_push($ar, "$key = NULL ");
			}
		}
		$sql .= implode(",", $ar);
		$sql .= " where 0 = 0 ";
		if($wherecol && $whereval){
			$sql .= " and ".$wherecol." = '".$whereval."'";
		}
		if($wpdb->query($sql) === FALSE){

			do_action('ajtodo_modify_project', $this->id);

			return false;
		}else{
			return true;
		}
	}

	public function updateCol($col, $val){
		global $table_prefix, $wpdb;
		$sql = "update ".AJTODO_DB_PROJECT." set $col = '".esc_sql($val)."' where id = ".$this->id;
		if($wpdb->query($sql) === FALSE){

			do_action('ajtodo_modify_project', $this->id);

			return false;
		}else{
			return true;
		}
	}

	public function editProject(){
		global $table_prefix, $wpdb;
		$sql = "update ".AJTODO_DB_PROJECT." set 
			title = '".$this->title."', 
			autoassign = '".($this->autoassign ? "Y" : "N")."', 
			comment = '".$this->comment."', 
			updated = '".date("Y-m-d H:i:s")."' where id = $this->id";
		$result = $wpdb->query($sql);
		if($result){

			do_action('ajtodo_modify_project', $this->id);

			return true;
		}else{
			return false;
		}
	}

	private function isUniqueKey($col, $val){
		global $table_prefix, $wpdb;
		$sql = "select count(id) from ".AJTODO_DB_PROJECT." where ".$col." = '".$val."'";
		return $wpdb->get_var($sql) == 0;
	}

	private function createTodoTable($pid){
		global $table_prefix, $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE ".AJTODO_DB_TODO."_".$pid." (
            id int(11) NOT NULL AUTO_INCREMENT,
            tkey varchar(100) NOT NULL,
			projectid int(11) NOT NULL,
			statuskey varchar(30) NOT NULL,
			categorykey varchar(30) NULL,
            planid int(11) NOT NULL DEFAULT 0,
            authorid int(11) NOT NULL,
            assignid int(11) NULL,
            title varchar(255) NOT NULL,
            comment longtext NOT NULL,
            metadata longtext NULL,
            todotype varchar(50) NULL,
            regdate datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            updated datetime,
            donedated datetime NULL,
            PRIMARY KEY  (id)
			) $charset_collate;";
		$wpdb->query($sql);
	}

	public function createProject(){
		global $table_prefix, $wpdb;
		$user = wp_get_current_user();
		$isautoassign = "N";
		if($this->projecttype == "private"){
			$isautoassign = "Y";
		}else{
			$isautoassign = $this->autoassign ? "Y" : "N";
		}
		if(!$this->pkey){
			$this->msg = __("프로젝트 키를 등록해주시기 바랍니다.","ajtodo");
			return false;
		}
		if(!$this->isUniqueKey("pkey", $this->pkey)){
			$this->msg = __("프로젝트 키가 중복됩니다. ","ajtodo");
			return false;
		}

		$sql = "insert into ".AJTODO_DB_PROJECT."(
			pkey,
			title, 
			comment, 
			projecttype,
			authorid, 
			autoassign,
			projectstatus, 
			roleperm,
			statuses,
			roles,
			todotype,
			regdate) values(
				'".$this->pkey."',
				'".$this->title."',
				'".$this->comment."',
				'".$this->projecttype."',
				".$user->ID.",
				'".$isautoassign."',
				'".AJTODO_PROJECT_STATUS_S."', ";
			$sql .= "'".AJTODO_ProjectDefault::DefaultRolePerms()."', ";
			$sql .= "'".AJTODO_ProjectDefault::DefaultStatus()."',
				'".AJTODO_ProjectDefault::DefaultRoles($user->ID)."',
				'".AJTODO_ProjectDefault::DefaultTodoType()."',
				'".date("Y-m-d H:i:s")."'
			)";
		$result = $wpdb->query($sql);
		if($result){
			$this->id = $wpdb->insert_id;
			$this->createTodoTable($this->id);
			$wpdb->query("insert into ".AJTODO_DB_PROJECTKEYS." values($this->id, 0);");
			
			do_action('ajtodo_add_project', $this->id);

			return true;
		}else{
			$this->msg = __("프로젝트 생성중에 문제가 발생했습니다. ","ajtodo");
			return false;
		}
	}

	public function getNewTodoIndex(){
		global $wpdb;
		return $wpdb->get_var("select todonewindex + 1 from ".AJTODO_DB_PROJECTKEYS." where projectid = ".$this->id);
	}

	public function setNewTodoIndex($newid){
		global $wpdb;
		$wpdb->query("update ".AJTODO_DB_PROJECTKEYS." set todonewindex = $newid where projectid = ".$this->id);
	}

	public function delProject(){
		global $wpdb;
		$user = wp_get_current_user();
		$wpdb->query("drop table ".AJTODO_DB_TODO."_".$this->id);
		$wpdb->query("delete from ".AJTODO_DB_PLAN." where projectid = ".$this->id);
		$wpdb->query("delete from ".AJTODO_DB_PROJECTKEYS." where projectid = ".$this->id);
		$wpdb->query("delete from ".AJTODO_DB_PROJECT." where authorid = $user->ID and id = ".$this->id);

		do_action('ajtodo_del_project', $this->id);

		return true;
	}

	public function doneProject(){
		global $table_prefix, $wpdb;
		$status = AJTODO_ProjectStatus::getSingleStatusByStatus($this->statuses, "D");
		$user = wp_get_current_user();
		$time = date("Y-m-d H:i:s");
		$wpdb->query("update ".AJTODO_DB_TODO."_".$this->id." set donedated = '$time', statuskey = '".$status["key"]."' where donedated is null");
		$wpdb->query("update ".AJTODO_DB_PROJECT." set donedate = '$time' where authorid = $user->ID and id = ".$this->id);
		
		do_action('ajtodo_done_project', $this->id);

		return true;
	}

	public function activeProject(){
		global $table_prefix, $wpdb;
		$user = wp_get_current_user();
		$time = date("Y-m-d H:i:s");
		$wpdb->query("update ".AJTODO_DB_PROJECT." set donedate = NULL where authorid = $user->ID and id = ".$this->id);

		do_action('ajtodo_active_project', $this->id);

		return true;
	}

	public function getNoProjectCount(){
		$this->percent = 0;
	}

	public function getCount(){
		global $table_prefix, $wpdb;
		$this->totalissue = $wpdb->get_var("select count(*) from ".AJTODO_DB_TODO."_".$this->id);
		$this->doneissue = $wpdb->get_var("select count(*) from ".AJTODO_DB_TODO."_".$this->id." where donedated is not null");
		$this->percent = 0;
		if($this->totalissue){
			$this->percent = round($this->doneissue * 100 / $this->totalissue, 0);
		}
	}

	public function getDoneConfirmView(){
		global $table_prefix, $wpdb;
		if($this->id){
			$this->getCount();
			$user = wp_get_current_user();
			$sql = "select * from ".AJTODO_DB_PROJECT." where 
				authorid = $user->ID and
				id = ".$this->id;
			$obj = $wpdb->get_row($sql);
			$obj->totalissue = $this->totalissue;
			$obj->doneissue = $this->doneissue;
			$obj->percent = round($this->percent);
			if($obj){
				$data = ajtodo_T($obj, $this->views["ProjectDoneConfirm"]);
				return ajtodo_T($this, $data);
			}else{
				return str_replace("[ALERT_MSG]", __("권한이 없습니다.","ajtodo"), $this->views["Alert"]);
			}
		}else{
			return str_replace("[ALERT_MSG]", __("잘못된 접근입니다.","ajtodo"), $this->views["Alert"]);
		}
	}

	public function getDelConfirmView(){
		global $table_prefix, $wpdb;
		if($this->id){
			$this->getCount();
			$user = wp_get_current_user();
			$sql = "select * from ".AJTODO_DB_PROJECT." where 
				authorid = $user->ID and
				id = ".$this->id;
			$obj = $wpdb->get_row($sql);
			$obj->totalissue = $this->totalissue;
			$obj->doneissue = $this->doneissue;
			if($obj){
				$data = ajtodo_T($obj, $this->views["ProjectDelConfirm"]);
				return ajtodo_T($this, $data);
			}else{
				return str_replace("[ALERT_MSG]", __("권한이 없습니다.","ajtodo"), $this->views["Alert"]);
			}
		}else{
			return str_replace("[ALERT_MSG]", __("잘못된 접근입니다.","ajtodo"), $this->views["Alert"]);
		}
	}

	public function getView(){
		if($this->id){
			$this->getCount();
			$this->comment = nl2br($this->comment);
			return $this->getCard();
		}
	}

	public function showMemberList(){
		$ret = "";
		$arrusers = is_array($this->roles) ? $this->roles : json_decode($this->roles, true);
		foreach(AJTODO_User::getUsersByRoles($arrusers) as $user){
			$ret .= "<div class='float-left inline_assignee' style='margin-right:4px;'>";
			$ret .= str_replace("<img ", "<img class='rounded-circle ajtodo_usersmallcard' title='".$user["name"]."' ", $user["avatar"]);
			$ret .= "</div>";
		}
		return $ret;
	}

	private function getCard(){
		$ret = "<tr>
			<td><a class='pkey' href='?page=ajtodo_admin_project&ptype=team&pid=$this->id'>$this->pkey</a></td>
			<td><a class='title' href='?page=ajtodo_admin_project&ptype=team&pid=$this->id'>$this->title</a></td>
			<td style='padding:0px;'>
				<div class='progress'>
					<div class='progress-bar' role='progressbar' style='width:$this->percent%;' aria-valuenow='$this->percent' aria-valuemin='0' aria-valuemax='100'>$this->percent%</div>
				</div>
			</td>
			<td class='text-center'>$this->doneissue/$this->totalissue</td>
			<td>".$this->showMemberList()."</td>";
		$ret .="</tr>";
		return $ret;
	}

	public function getButton($rolename){
		if($this->ajtodo_roles->roles[$rolename]){
			return $this->buttons[$rolename];
		}else{
			return "";
		}
	}

	private function getUserInfo($uid){
		$arrusers = is_array($this->roles) ? $this->roles : json_decode($this->roles, true);
		foreach(AJTODO_User::getUsersByRoles($arrusers) as $user){
			if($user["id"] == $uid)
				return $user;
		}
		return null;
	}

	public function getDailyRemainIssues($planid = ""){
		global $wpdb;
		$labels = array();
		$datasets = array();
		if($planid){
			$pl = new AJTODO_ProjectPlan();
			$planInfo = $pl->getPlan($planid);
			$begin = new DateTime(substr($planInfo->startdate, 0, 10));
			$end   = new DateTime(substr($planInfo->finishdate, 0, 10));
			for($i = $begin; $i <= $end; $i->modify('+1 day')){
				$labels[] = $i->format("m/d");
			}
			$sql = "select DATE(donedated) as x, count(*) as y from ".AJTODO_DB_TODO."_".$this->id;
			$sql .= " where donedated is not null ";
			if($planid)
				$sql .= " and planid = ".$planid;
			$sql .= " group by x";
			$list = $wpdb->get_results($sql);
			$data = array();
			$bg = array();
			$bd = array();
			foreach($labels as $label){
				$setval = false;
				foreach($list as $item){
					$newx = new DateTime($item->x);
					if($label == $newx->format("m/d")){
						$data[] = $item->y;
						$setval = true;
						break;
					}
				}
				if(!$setval)
					$data[] = 0;
				$bg[] = "rgba(54, 162, 235, 0.2)";
				$bd[] = "rgba(54, 162, 235, 1)";
			}
			$datasets[] = array(
				"data" =>  $data,
				"backgroundColor" => $bg,
				"borderColor" => $bd,
				"borderWidth" =>  1
			);
		}
		return array("labels" => $labels, "datasets" =>  $datasets);
	}

	public function getPlanSelector($planid = ""){
		global $wpdb;
		$nowplaninfo = "";
		if($planid){
			foreach($this->plan as $p){
				if($planid == $p["id"]){
					$nowplaninfo = $p;
					break;
				}
			}
		}
		$link = "?page=ajtodo_admin_project&ptype=team&ajtodo_type=report&pid=".$this->id;
		$ret = "<div class='dropdown'>";
		$ret .= "<button type='button' class='btn btn-primary fs12 dropdown-toggle' data-toggle='dropdown'>";
		if($planid){
			if($nowplaninfo["donedate"] != ""){
				$ret .= "<i class='far fa-check-circle'></i> ";
			}else if($nowplaninfo["ising"] == "Y"){
				$ret .= "<i class='fas fa-play'></i> ";
			}else{
				$ret .= "<i class='far fa-circle'></i> ";
			}
			$ret .= $nowplaninfo["plantitle"];
		}else{
			$ret .= __("전체", "ajtodo");
		}
		$ret .= "</button>";
		$ret .= "<div class='dropdown-menu' style='padding:0'>";
		foreach($this->plan as $p){
			if($planid == $p["id"])
				continue;

			$ret .= "<a class='dropdown-item fs12' style='padding:8px;' href='".$link."&planid=".$p["id"]."'>";
			if($p["donedate"] != ""){
				$ret .= "<i class='far fa-check-circle'></i> ";
			}else if($p["ising"] == "Y"){
				$ret .= "<i class='fas fa-play'></i> ";
			}else{
				$ret .= "<i class='far fa-circle'></i> ";
			}
			$ret .= $p["plantitle"];
			$ret .= "</a>";
		}
		if($planid){
			$ret .= "<div class='dropdown-divider' style='margin:0px'></div>";
			$ret .= "<a class='dropdown-item fs12' style='padding:8px;' href='".$link."'>";
			$ret .= __("전체", "ajtodo");
			$ret .= "</a>";
		}
		$ret .= "</div>";
		$ret .= "</div>";
		return $ret;
	}
	public function quickLink($planid = ""){
		$link = "?page=ajtodo_admin_project&ptype=team&ajtodo_type=plan&pid=".$this->id;
		if($planid)
			$link .= "&plankey=".$planid;
		$ret = "<div class='btn-group' role='group' aria-label='Basic example'>";
		if($planid){
			$ret .= "<a href='".$link."' class='btn btn-secondary'>".__("플랜 뷰","ajtodo")."</a>";
			$ret .= "<a href='".$link."&act=addplan' class='btn btn-secondary'>".__("플랜 관리","ajtodo")."</a>";
		}
		$ret .= "</div>";
		return $ret;
	}

	public function getLinkedDocs($planid = ""){
		global $wpdb;
		$adddoclink = "/wp-admin/post-new.php?post_type=ajtododoc&pid=".$this->id;
		if($planid)
			$adddoclink .= "&planid=".$planid;

		$ret = "<div class='aj_item'>";
		$ret .= "<div class='card aj_item-content'>";
		$ret .= "<div class='card-header'>";
		$ret .= __("최근 문서","ajtodo");
		if($this->hasPerm("tp_doc_create")){
			$ret .= "<a href='".$adddoclink."' class='btn btn-sm btn-info float-right text-white' style='margin:-4px'><i class='fas fa-plus'></i> ".__("문서 추가","ajtodo");
			$ret .= "</a>";
		}
		$ret .= "</div>";
		$ret .= "	<div class='card-body'>";

		$ttype = new AJTODO_ProjectLink();
		$ttype->project = $this;
		$list = $planid ? $ttype->getLinkedDocsByPlan($planid) : $ttype->getLinkedDocsByProject();

		if(!count($list)){
			$ret .= "<div class=''>";
			$ret .= __("문서 없음", "ajtodo");
			$ret .= "</div>";
		}else{
			$ret .= "<ul class='list-group'>";
			foreach($list as $item){
				$post = get_post($item->postid);
				$ret .= "	<li class='list-group-item aj_doc_listitem'>";
				$ret .= str_replace("<img ", "<img class='rounded-circle' ", get_avatar($post->post_author, 24));
				$ret .= "	<a href='".get_permalink($item->postid)."' target='_blank'>";
				$ret .= "<span style='margin-left:4px;' class='fs12'> ".$post->post_title."</span>";
				$ret .= "</a>";
				$ret .= " <kbd class='float-right'>".date("m/d H:i:s", strtotime($post->post_modified))."</kbd>";
				$ret .= "	</li>";
			}
			$ret .= "</ul>";
		}

		$ret .= "	</div>";
		$ret .= "</div>";
		$ret .= "</div>";
		return $ret;
	}

	public function getReportIssueByTodoType($planid = ""){
		global $wpdb;
		$ret = "<div class='aj_item'>";
		$ret .= "<div class='card aj_item-content'>";
		$ret .= "<div class='card-header'>";
		$ret .= __("할일 타입별 할일 현황","ajtodo");
		$ret .= "</div>";
		$ret .= "	<div class='card-body'>";

		$ttype = new AJTODO_ProjectTodoType();
		$ttype->project = $this;

		$totaltodo = $wpdb->get_var("select count(id) from ".AJTODO_DB_TODO."_".$this->id.($planid ? " where planid = $planid " : ""));
		$sql = "SELECT todotype, count(*) as cnt FROM ".AJTODO_DB_TODO."_".$this->id;
		if($planid)
			$sql .= " where planid = ". $planid;
		$sql .= " group by todotype";
		$list = $wpdb->get_results($sql);

		$ret .= "<table style='width:100%;'>";
		foreach($list as $item){
			$t = $ttype->getSingleTodoType($item->todotype);
			$per = $item->cnt * 100 / $totaltodo;
			$ret .= "	<tr>";
			$ret .= "		<td width='15%' style='min-width:120px;'>";
			$ret .= "<div style='padding: 5px 0px;'>".$t["name"]."</div>";
			$ret .= "</td>";
			$ret .= "		<td width='85%'>";
			$ret .= "<div class='progress' style='height:30px;'><div class='progress-bar text-left' style='width:".$per."%';height30px;'>".$item->cnt."</div><div>";
			$ret .= "		</td>";
			$ret .= "	</tr>";
		}
		$ret .= "</table>";

		$ret .= "	</div>";
		$ret .= "</div>";
		$ret .= "</div>";
		return $ret;
	}

	public function getReportIssueByCategory($planid = ""){
		global $wpdb;
		$ret = "<div class='aj_item'>";
		$ret .= "<div class='card aj_item-content'>";
		$ret .= "<div class='card-header'>";
		$ret .= __("카테고리별 할일 현황","ajtodo");
		$ret .= "</div>";
		$ret .= "	<div class='card-body'>";

		$ttype = new AJTODO_ProjectCategory();
		$ttype->project = $this;

		$totaltodo = $wpdb->get_var("select count(id) from ".AJTODO_DB_TODO."_".$this->id.($planid ? " where planid = $planid " : ""));
		$sql = "SELECT categorykey, count(*) as cnt FROM ".AJTODO_DB_TODO."_".$this->id;
		if($planid)
			$sql .= " where planid = ". $planid;
		$sql .= " group by categorykey";
		$list = $wpdb->get_results($sql);

		$ret .= "<table style='width:100%;'>";
		foreach($list as $item){
			$c = $ttype->getSingleCategory($item->categorykey);
			$per = $item->cnt * 100 / $totaltodo;
			$ret .= "	<tr>";
			$ret .= "		<td width='15%' style='min-width:120px;'>";
			if($c){
				$ret .= "<div style='padding: 5px 0px;'>".$c["name"]."</div>";
			}else{
				$ret .= "<div style='padding: 5px 0px;'>".__("카테고리 없음","ajtodo")."</div>";
			}
			$ret .= "</td>";
			$ret .= "		<td width='85%'>";
			$ret .= "<div class='progress' style='height:30px;'><div class='progress-bar text-left' style='width:".$per."%';height30px;'>".$item->cnt."</div><div>";
			$ret .= "		</td>";
			$ret .= "	</tr>";
		}
		$ret .= "</table>";

		$ret .= "	</div>";
		$ret .= "</div>";
		$ret .= "</div>";
		return $ret;
	}

	public function getReportIssueByUsers($planid = ""){
		global $wpdb;
		$ret = "<div class='aj_item'>";
		$ret .= "<div class='card aj_item-content'>";
		$ret .= "<div class='card-header'>";
		$ret .= __("멤버별 할일처리 현황","ajtodo");
		$ret .= "</div>";
		$ret .= "	<div class='card-body'>";

		$totaltodo = $wpdb->get_var("select count(id) from ".AJTODO_DB_TODO."_".$this->id.($planid ? " where planid = $planid " : ""));
		$sql = "SELECT assignid, count(*) as cnt FROM ".AJTODO_DB_TODO."_".$this->id;
		if($planid)
			$sql .= " where planid = $planid ";
		$sql .= " group by assignid";
		$assignlist = $wpdb->get_results($sql);

		$ret .= "<table style='width:100%;'>";
		foreach($assignlist as $item){
			$u = $this->getUserInfo($item->assignid);
			$per = $item->cnt * 100 / $totaltodo;
			$ret .= "	<tr>";
			$ret .= "		<td width='15%' style='min-width:120px;white-space: nowrap;'>";
			if($u){
				$ret .= str_replace("<img ", "<img class='rounded-circle ajtodo_usersmallcard' style='margin-right:4px;' ", $u["avatar"]).$u["name"];
			}else{
				$ret .= "<div style='padding: 5px 0px;'>".__("담당자 없음","ajtodo")."</div>";
			}
			$ret .= "</td>";
			$ret .= "		<td width='85%'>";
			$ret .= "<div class='progress' style='height:30px;'><div class='progress-bar text-left' style='width:".$per."%';height30px;' aria-valuemin='0' aria-valuemax='100'>".$item->cnt."</div><div>";
			$ret .= "		</td>";
			$ret .= "	</tr>";
		}
		$ret .= "</table>";

		$ret .= "	</div>";
		$ret .= "</div>";
		$ret .= "</div>";
		return $ret;
	}

	public function getDailRemainChart($planid = ""){
		$ret = "<div class='aj_item ajtodo_dailyDoneChart'>";
		$ret .= "<div class='card aj_item-content'>";
		$ret .= "<div class='card-header'>";
		$ret .= __("일별 완료개수","ajtodo");
		$ret .= "</div>";
		$ret .= "	<div class='card-body'>";
		$ret .= "		<canvas id='dailyDoneChart' style='width:300px;height:150px;'>";
		$ret .= " 		</canvas>";
		$ret .= " 	</div>";
		$ret .= "</div>";
		$ret .= "</div>";
		return $ret;
	}

	public function getReportSummary($planid = "", $freez = false){
		$nowplaninfo = "";
		if($planid){
			foreach($this->plan as $p){
				if($planid == $p["id"]){
					$nowplaninfo = $p;
					break;
				}
			}
		}
		$ret = "<div class='".($freez ? "aj_item_view" : "aj_item")."'>";
		$ret .= "<div class='card aj_item-content'>";
		$ret .= "<div class='card-header'>";
		if($nowplaninfo){
			$ret .= __("플랜 요약","ajtodo");
			if($nowplaninfo["ising"] == "Y"){
				$ret .= " <span class='badge badge-light float-right'>".__("진행중","ajtodo")."</span>";
			}else if($nowplaninfo["donedate"]){
				$ret .= " <span class='badge badge-light float-right'>".__("완료됨","ajtodo")."</span>";
			}
		}else{
			$ret .= __("프로젝트 요약","ajtodo");
		}
		$ret .= "</div>";
		$ret .= "	<div class='card-body'>";
		if($nowplaninfo){
			$ret .= "<p style='margin-bottom:4px;'>".$nowplaninfo["plancomment"]."</p>";

			$ret .= "		<p class='card-text' style='margin-bottom: 8px;'>".__("시작일")." : ".substr($nowplaninfo["startdate"],0, 10);
			if($nowplaninfo["donedate"]){
				$ret .= " ~ ".__("완료일")." : ".substr($nowplaninfo["donedate"],0, 10)." (".__("목표일")." : ".substr($nowplaninfo["finishdate"],0, 10).")";
			}else{
				$ret .= " ~ ".__("목표일")." : ".substr($nowplaninfo["finishdate"],0, 10);
			}
			$ret .= "		</p>";
			if(!$nowplaninfo["donedate"]){
				$ret .= "		".$this->getPlanProgress2($nowplaninfo["id"])."";
			}
		}else{
			$ret .= "<p style='margin-bottom:4px;'>".nl2br($this->comment)."</p>";
		}
		$ret .= "		<div style='margin-top:12px'>";
		$ret .= $this->showMemberList();
		$ret .= "		</div>";
		$ret .= "	</div>";
		$ret .= "</div>";
		$ret .= "</div>";
		return $ret;
	}

	public function getPlanProgress2($plankey){
		global $wpdb;
		$ret = array();
		$sql = "SELECT statuskey, count(*) as cnt FROM ".AJTODO_DB_TODO."_".$this->id." where planid = ".$plankey;
		$sql .= " group by statuskey";
		$statuslist = $wpdb->get_results($sql, ARRAY_A);
		$closed = 0;
		$open = 0;
		$ing = 0;
		$all = 0;
		foreach($statuslist as $status){
			if($status['statuskey'] == "closed"){
				$closed += $status['cnt'];
			}else if($status['statuskey'] == "open"){
				$open += $status['cnt'];
			}else{
				$ing += $status['cnt'];
			}
			$all += $status['cnt'];
		}
		$perc_o = 0; $perc_i = 0;
		if($all > 0){
			$perc_o = round(($closed * 100) / $all);
			$perc_i = round(($ing * 100) / $all);
		}
		$ret = "<div class='progress'>";
		$ret .= "<div class='progress-bar bg-success' role='progressbar' ";
		$ret .= " style='width: $perc_o%;' aria-valuenow='$perc_o' aria-valuemin='0' ";
		$ret .= " aria-valuemax='100'></div>";
		$ret .= "<div class='progress-bar bg-warning' role='progressbar' ";
		$ret .= " style='width: $perc_i%;' aria-valuenow='$perc_i' aria-valuemin='0' ";
		$ret .= " aria-valuemax='100'></div>";
		$ret .= "</div>";
		return $ret;
	}
}
