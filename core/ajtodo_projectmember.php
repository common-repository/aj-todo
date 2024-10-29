<?php
class AJTODO_ProjectMember{
	public $project;
	public $msg = "";
	public $project_basic_perms = array();
	public $project_todo_perms = array();

	public function __construct(){
	}

	public function setInitRoles($uid){
		$ret = array(
			array("key" => "project_admin", "name" => __("관리자", "ajtodo"), "users" => array($uid)),
			array("key" => "project_manager", "name" => __("매니저", "ajtodo"), "users" => array($uid)),
			array("key" => "project_user", "name" => __("사용자", "ajtodo"), "users" => array($uid)),
		);
		return $ret;
	}

	public static function getMembers(){
		$ret = array();
		foreach($this->project->roles as $role){
			$ret = array_unique(array_merge($$ret, $role["users"]));
		}

		$ret = apply_filters('ajtodo_get_allprojectuserIDs', $ret, $this->project->id);

		return $ret;
	}

	private function setMessage(){
		$ret = "<div class='alert alert-info'>";
		$ret .= __("역할은 '프로젝트 멤버' 탭에서 생성할 수 있습니다.","ajtodo");
		$ret .= "</div>";
		return $ret;
	}

	public function memberListView(){
		//$user = wp_get_current_user();
		//$this->project->updateCol("roleperm", json_encode($this->setInitRoles($user->ID), JSON_UNESCAPED_UNICODE));
		$ret = "";
		$ret .= "<form id='ajtodo_form'>";
		$ret .= "<input type='hidden' name='dotype'>";
		$ret .= $this->createFormPart(
			__("프로젝트 역할/멤버","ajtodo")
		);
		if($this->project->hasPerm("tp_role_member") && !$this->project->donedate){
			$ret .="<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=member&act=addrole&pid=".$this->project->id."' class='btn btn-info'>".__('역할 추가', 'ajtodo' )."</a>";
		}
		$ret .= "</form>";
		return $ret;
	}

	public static function getUserSmallCard($id){
		$ret = "";
		if($id){
			$user = get_userdata($id);
			$ret = "<table class='ajtodo_small_usercard float-left'><tr>";
			$ret .= "<td>".get_avatar($user->ID, 18)."</td>";
			$ret .= "<td style='padding-right:8px !important;'>".$user->display_name."</td>";
			$ret .= "</tr></table>";
		}
		return $ret;
	}

	public function createFormPart($title){
		$ret = "";
		$ret .= "<div class='fs18'>".$title."</div>";
		$ret .= "<table class='table ajtodo_members'>";
		$ret .= "<thead  class='thead-light'>";
		$ret .= "	<tr>";
		$ret .= "		<th scope='col' style='width:150px;'>".__("역할","ajtodo")."</th>";
		$ret .= "		<th scope='col'>".__("멤버","ajtodo")."</th>";
		if(!$this->project->donedate){
			$ret .= "		<th scope='col' style='width:250px;'>".__("관리","ajtodo")."</th>";
		}
		$ret .= "	</tr>";
		$ret .= "</thead>";

		$ret .= "<tbody id='ajtodo_role_table'>";
		foreach($this->project->roles as $role){
			$ret .= "<tr>";
			$ret .= "	<td scope='col'>".$role["name"]."</td>";
			$ret .= "	<td scope='col'>";
			foreach($this->getUsersByRole($role["key"]) as $mem){
				$ret .= $this->getUserSmallCard($mem);
			}
			$ret .=	"	<div class='clearfix'></div>";
			$ret .=	"	</td>";
			if(!$this->project->donedate){
				$ret .= "	<td scope='col'>";
				if($this->project->hasPerm("tp_invite")){
					$ret .= "		<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=member&act=addmember&rolekey=".$role["key"]."&pid=".$this->project->id."' class='btn btn-success'>".__('사용자 추가', 'ajtodo' )."</a>";
				}
				if($this->project->hasPerm("tp_role_member")){
					$ret .= "		<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=member&act=addrole&rolekey=".$role["key"]."&pid=".$this->project->id."' class='btn btn-info'>".__('역할 관리', 'ajtodo' )."</a>";
				}
				$ret .= "	</td>";
			}
			$ret .= "<tr>";
		}
		$ret .= "</tbody>";
		$ret .= "</table>";
		return $ret;
	}

	public function getUsersByRole($role){
		foreach($this->project->roles as $r){
			if($r["key"] == $role){
				return $r["users"];
			}
		}
		return array();
	}

	public function delRole($rolekey){
		$delindex = -1;
		for($i = 0; $i < count($this->project->roles) ; $i++){
			if($this->project->roles[$i]["key"] == $rolekey){
				$delindex = $i;
			}
		}
		for($i = 0; $i < count($this->project->statuses) ; $i++){
			for($j = 0; $j < count($this->project->statuses[$i]["rules"]) ; $j++){
				if (($key = array_search($rolekey, 
								$this->project->statuses[$i]["rules"][$j]["roles"])) !== false) {
					unset($this->project->statuses[$i]["rules"][$j]["roles"][$key]);
				}
			}
		}
		if($delindex != -1){
			unset($this->project->roles[$delindex]);
		}
		unset($this->project->roleperm[$rolekey]);

		$ret = $this->project->updateCols(
			array(
				"roles" => json_encode($this->project->roles, JSON_UNESCAPED_UNICODE),
				"roleperm" => json_encode($this->project->roleperm, JSON_UNESCAPED_UNICODE),
				"statuses" => json_encode($this->project->statuses, JSON_UNESCAPED_UNICODE),
			)
		);

		if($ret)
			do_action('ajtodo_del_role', $this->project->id);

		return $ret;
	}

	public function addRole($dotype, $rolekey, $rolename){
		if($dotype == "add"){
			$has = false;
			foreach($this->project->roles as $r){
				if($r["key"] == $rolekey){
					$has = true;
				}
			}
			if($has){
				$this->msg = __("역할 키가 중복됩니다. ","ajtodo");
				return false;
			}

			$this->project->roles[] = array(
					"key" => $rolekey, 
					"name" => $rolename, 
					"users" => array());
		}else{
			foreach($this->project->roles as &$r){
				if($r["key"] == $rolekey){
					$r["name"] = $rolename;
				}
			}
		}

		$ret = $this->project->updateCol("roles", json_encode($this->project->roles, JSON_UNESCAPED_UNICODE));
		
		if($ret){
			if($dotype == "add"){
				do_action('ajtodo_add_role', $this->project->id, $rolekey, $rolename);
			}else{
				do_action('ajtodo_modify_role', $this->project->id, $rolekey, $rolename);
			}
		}

		return $ret;
	}

	public function addMemberRole($role, $users){
		$re = $this->project->roles ? $this->project->roles : array();
		$set = false;
		foreach($re as &$r){
			if($r["key"] == $role){
				$r["users"] = explode(",", $users);
				$set = true;
			}
		}

		$ret = $this->project->updateCol("roles", json_encode($re, JSON_UNESCAPED_UNICODE));
		
		if($ret){
			do_action('ajtodo_add_memberinrole', $this->project->id, $role, $users);
		}

		return $ret;
	}

	private function getUserCardForEdit($id){
		$user = get_userdata($id);
		$userbox = "<div class='btn-group' role='group' val=".$id.">";
		$userbox .= "<button type='button' class='disabled btn btn-secondary'>".$user->display_name."</button>";
		$userbox .= "<button type='button' class='btn btn-danger'><i class='far fa-minus-square'></i></button>";
		$userbox .= "</div>";
		return $userbox;
	}

	private function getRoleNameByKey($rolekey){
		$ret = "";
		if($rolekey){
			foreach($this->project->roles as $r){
				if($r["key"] == $rolekey){
					$ret = $r["name"];
				}
			}
		}
		return $ret;
	}

	public function addRoleView($rolekey){
		$rolename = $this->getRoleNameByKey($rolekey);
		$users = get_users( array('fields' => array('ID', 'display_name')));
		$ret = "";
		$ret .= "<div class='fs18 mb8'>";
		if($rolekey){
			$ret .= __("프로젝트 역할 수정","ajtodo");
		}else{
			$ret .= __("프로젝트 역할 추가","ajtodo");
		}
		$ret .= "</div>";
		$ret .= "<form id='ajtodo_form' method='post'>";
		$ret .= "<input type='hidden' id='dotype' name='dotype' value='".($rolekey ? "update" : "add")."'>";
		
		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('역할 Key', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option'>";
		if($rolekey){
			$ret .= "<input type='hidden' class='form-control' 
				name='ajtodo_role_key' id='ajtodo_role_key' value='".$rolekey."'>";
			$ret .= "<span class='font-italic'>".$rolekey."</span>";
		}else{
			$ret .= "<input type='text' class='form-control form-control-sm' maxlength='10' 
				name='ajtodo_role_key' id='ajtodo_role_key' style='width:150px'>";
			$ret .= "<p>".__("10자 이내의 영문자와 숫자만 가능합니다.","ajtodo")."</p>";
		}
		$ret .= "	</div>";
		$ret .= "</div>";

		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('역할 이름', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option'>";
		$ret .= "<input type='text' class='form-control form-control-sm' 
			name='ajtodo_role_name' id='ajtodo_role_name' value='".$rolename."' style='width:250px'>";
		$ret .= "	</div>";
		$ret .= "</div>";

		$ret .= "<a href='#' id='ajtodo_update_role' class='btn btn-primary'>".__('변경 사항 적용', 'ajtodo' )."</a>";

		if($rolekey && !$this->IsBuiltIn($rolekey)){
			$ret .= "<a href='#' id='ajtodo_del_role' class='btn btn-danger'>".__('삭제', 'ajtodo' )."</a>";
		}
		$ret .= "<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=member&pid=".$this->project->id."' class='btn btn-success'>".__('멤버 목록', 'ajtodo' )."</a>";
		$ret .= "</form>";
		return $ret;
	}

	private function IsBuiltIn($rolekey){
		foreach(json_decode(AJTODO_ProjectDefault::DefaultRoles(""), true) as $role){
			if($role["key"] == $rolekey)
				return true;
		}
		return false;
	}

	public function addMemberView($rolekey){
		$rolename = $this->getRoleNameByKey($rolekey);
		$users = get_users( array('fields' => array('ID', 'display_name')));
		$ret = "";
		$ret .= "<div class='fs18 mb8'>".__("프로젝트 사용자 역할 관리","ajtodo")."</div>";
		$ret .= "<form id='ajtodo_form' method='post'>";
		$ret .= "<input type='hidden' name='dotype' value='up'>";
		$ret .= "<input type='hidden' name='ajtodo_users' id ='ajtodo_users'>";
		$ret .= "<input type='hidden' name='role' id ='role' value='$rolekey'>";
		
		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('역할', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option'>".$rolename."</div>";
		$ret .= "</div>";

		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('추가할 사용자', 'ajtodo' )."</label>";
		$ret .= "	<div class='input-group'>";
		$ret .= "		<select name='users' id='ajtodo_sel_member'>";
		foreach($users as $u){
			$ret .= "		<option value='".$u->ID."'>".$u->display_name."</option>";
		}
		$ret .= "		</select>";
		$ret .= "		<div class='input-group-append'>";
		$ret .= "		<button id='ajtodo_update_addthismember' class='btn btn-primary'>".__('추가', 'ajtodo' )."</button>";
		$ret .= "		</div>";
		$ret .= "	</div>";
		$ret .= "</div>";

		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('추가된 사용자', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option' id='ajtodo_addmember_ing'>";
		foreach($this->getUsersByRole($rolekey) as $mem){
			$ret .= $this->getUserCardForEdit($mem);
		}
		$ret .= "	</div>";
		$ret .= "</div>";

		$ret .= "<a href='#' id='ajtodo_update_memberrole' class='btn btn-primary'>".__('변경 사항 적용', 'ajtodo' )."</a>";
		$ret .= "<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=member&pid=".$this->project->id."' class='btn btn-success'>".__('멤버 목록', 'ajtodo' )."</a>";
		$ret .= "</form>";
		return $ret;
	}
	
	public function update(){
		return "role";
	}
}
