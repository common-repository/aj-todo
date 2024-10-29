<?php
class AJTODO_Role{
	public $init_globalroles = array();

	public function __construct(){
		$this->setData();
	}

	private function setData(){
		$this->init_globalroles = array(
			array(
				"key" => "ajtodo_project_team_create", 
				"name" => __("팀 프로젝트 생성","ajtodo"), 
				"desc" => __("팀 프로젝트를 생성할 수 있습니다.","ajtodo")
			),
			array(
				"key" => "ajtodo_project_team_remove", 
				"name" => __("팀 프로젝트 삭제","ajtodo"), 
				"desc" => __("팀 프로젝트를 삭제할 수 있습니다.","ajtodo")
			),
			array(
				"key" => "ajtodo_project_team_close", 
				"name" => __("팀 프로젝트 종료","ajtodo"), 
				"desc" => __("팀 프로젝트를 종료할 수 있습니다.","ajtodo")
			),
			array(
				"key" => "ajtodo_project_team_viewlist", 
				"name" => __("팀 프로젝트 목록 보기","ajtodo"), 
				"desc" => __("프로젝트 톡록을 열람할 수 있습니다.","ajtodo")
			),
			array(
				"key" => "ajtodo_project_private", 
				"name" => __("개인 프로젝트 사용","ajtodo"), 
				"desc" => __("개인 프로젝트 기능을 사용할 수 있습니다.","ajtodo")
			)
		);
	}

	private function clearPerm(){
		$this->clearRolePerm("administrator");
		$this->clearRolePerm("editor");
		$this->clearRolePerm("author");
		$this->clearRolePerm("contributor");
		$this->clearRolePerm("subscriber");
	}

	private function clearRolePerm($role){
		foreach($this->init_globalroles as $r){
			$this->updatePerms($role, $r["key"], false);
		}
	}

	public function updateRolesPerms($postdata){
		$this->clearPerm();
		foreach($postdata as $key => $val){
			$pv = isset($key) ? sanitize_text_field($key) : "";
			if(strstr($pv, "|")){
				$tmp = explode("|", $pv);
				$this->updatePerms($tmp[0], $tmp[1], true);
			}
		}
	}
	private function updatePerms($rolekey, $cap, $isadd){
		$role = get_role($rolekey);
		$isadd ? $role->add_cap($cap) : $role->remove_cap($cap);
	}

	public function getFormRolesPerms(){
		global $wp_roles;
		$wproles = $wp_roles->roles;
		$ret = "";
		$ret .= $this->getFormRolePerm("administrator", "Administrator", $wproles);
		$ret .= $this->getFormRolePerm("editor", "Editor", $wproles);
		$ret .= $this->getFormRolePerm("author", "Author", $wproles);
		$ret .= $this->getFormRolePerm("contributor", "Contributor", $wproles);
		$ret .= $this->getFormRolePerm("subscriber", "Subscriber", $wproles);
		return $ret;
	}

	private function getFormRolePerm($role, $rolename, $wproles){
		$ret = "<tr><td>".translate_user_role($rolename)."</td>";
		foreach($this->init_globalroles as $r){
			$ret .= "<td>";
			if(array_key_exists($r["key"], $wproles[$role]["capabilities"])){
				$ret .= "<input type='checkbox' name='".$role."|".$r["key"]."' checked>";
			}else{
				$ret .= "<input type='checkbox' name='".$role."|".$r["key"]."'>";
			}
			$ret .= "</td>";
		}
		$ret .= "</tr>";
		return $ret;
	}

	public function setInitData(){
		$role = get_role("administrator");
		$role->add_cap('ajtodo_project_team_create', true);
		$role->add_cap('ajtodo_project_team_remove', true);
		$role->add_cap('ajtodo_project_team_close', true);
		$role->add_cap('ajtodo_project_team_viewlist', true);
		$role->add_cap('ajtodo_project_private', true);

		$role = get_role("editor");
		$role->add_cap('ajtodo_project_private', true);

		$role = get_role("author");
		$role->add_cap('ajtodo_project_private', true);
	}

	private function initInsert($role){
		global $wpdb;
		$in = $wpdb->get_var("select count(id) from ".AJTODO_DB_GLOBALROLE." where rolename = '$role'");
		if(!$in){
			$sql = "inert into ".AJTODO_DB_GLOBALROLE;
			$sql .=" (rolename, roledesc, wprole, regdate) values";
			$sql .=" (rolename, roledesc, wprole, regdate);";
			$wpdb->query($sql);
		}
	}

	public function getProjectRoles(){
		if(is_user_logged_in()){
			$this->roles["ViewProjectList"] = true;
			$this->roles["ViewTodoList"] = true;
			$this->roles["CreateTodo"] = true;
			$this->roles["UpdateStatusTodo"] = true;
			$this->roles["DelTodo"] = true;
			$this->roles["QuickCreateTodo"] = true;
			$this->roles["Hello"] = true;
		}
	}

	public function getGlobalRoles(){
		global $wpdb;
		return $wpdb->get_results("select * from ".AJTODO_DB_GLOBALROLE);	
	}

	public function setRoles(){
		if(is_user_logged_in()){
			$this->roles["CreateTeamProject"] = true;
			$this->roles["DelPrivateProject"] = true;
			$this->roles["DelTeamProject"] = true;
			$this->roles["DoneTeamProject"] = true;
			$this->roles["CreateProject"] = true;
			$this->roles["ViewTodoList"] = true;
			$this->roles["CreateTodo"] = true;
			$this->roles["UpdateStatusTodo"] = true;
			$this->roles["DelTodo"] = true;
			$this->roles["QuickCreateTodo"] = true;
			$this->roles["Hello"] = true;
		}
	}
}
