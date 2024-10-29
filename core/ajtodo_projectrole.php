<?php
class AJTODO_ProjectRole{
	public $project;

	public function __construct(){
	}

	public function getInitRolePerms(){
	}

	private function setMessage(){
		$ret = "<div class='alert alert-info'>";
		$ret .= __("역할은 '프로젝트 멤버' 탭에서 생성할 수 있습니다.","ajtodo");
		$ret .= "</div>";
		return $ret;
	}

	public function createForm(){
		$ret = "";
		$ret .= $this->setMessage();
		$ret .= "<form method='post'>";
		$ret .= "<input type='hidden' name='dotype' value='up'>";
		$ret .= wp_nonce_field('ajtodo_nonce','update_roleperm');
		$ret .= $this->createFormPart(
				__("프로젝트 관리","ajtodo"),
				AJTODO_ProjectDefault::DefaultProjectPerms()
		);
		$ret .= $this->createFormPart(
				__("플랜","ajtodo"),
				AJTODO_ProjectDefault::DefaultPlanPerms()
		);
		$ret .= $this->createFormPart(
				__("할일","ajtodo"),
				AJTODO_ProjectDefault::DefaultTodoPerms()
		);
		$ret .= $this->createFormPart(
				__("문서","ajtodo"),
				AJTODO_ProjectDefault::DefaultDocPerms()
		);
		if(!$this->project->donedate){
			$ret .="<button id='ajtodobtn_save' class='btn btn-primary'>".__('변경 사항 적용', 'ajtodo' )."</button>";
		}
		$ret .= "</form>";
		return $ret;
	}

	public function createFormPart($title, $perms){
		$ret = "";
		$ret .= "<div class='fs18'>".$title."</div>";
		$ret .= "<table class='table table-striped ajtodo_perms'>";
		$ret .= "<thead  class='thead-light'>";
		$ret .= "	<tr>";
		$ret .= "		<th scope='col'>".__("권한","ajtodo")."</th>";
		foreach($this->project->roles as $role){
			$ret .=  "	<th style='width:150px;' scope='col' class='text-center'>".$role["name"]."</th>";
		}
		$ret .= "	</tr>";
		$ret .= "</thead>";

		$ret .= "<tbody>";
		foreach($perms as $perm){
			$ret .= "	<tr>";
			$ret .= "		<td>".$perm["name"]."</td>";
			foreach($this->project->roles as $role){
				$hasRole = $this->hasPerm($role["key"], $perm["key"]);
				$ret .= "	<td class='text-center'><input type='checkbox' 
					name='".$role["key"]."|".$perm["key"]."' ";
				if($hasRole){
					$ret .= " checked ";
				}
				$ret .= ($this->project->donedate ? " disabled " : "");
				$ret .= " ></td>";
			}
			$ret .= "	</tr>";
		}
		$ret .= "</tbody>";
		$ret .= "</table>";
		return $ret;
	}

	private function hasPerm($role, $perm){
		if(array_key_exists($role, $this->project->roleperm)){
			return in_array($perm, $this->project->roleperm[$role]);
		}else{
			return false;
		}
	}

	private function updatePermsByRole($arr, $role, $perm){
		$in = false;
		foreach($arr as $r){
			if($r["key"] == $role){
				array_push($r["perms"], $perm);
				$in = true;
			}
		}
		if(!$in){
			$arr[] = array("key" => $role, "perms" => array($perm));
		}

		return $arr;
	}
	
	public function updateRolePerms($data){
		$arr = array();
		foreach($data as $key => $val){
			if(strstr($key, "|")){
				$tmp = explode("|", $key);
				$role = $tmp[0];
				$arr[$tmp[0]][] = $tmp[1];
			}
		}
		
		$ret = $this->project->updateCol("roleperm", json_encode($arr, JSON_UNESCAPED_UNICODE));
		if($ret)
			do_action('ajtodo_modify_roleperms', $this->project->id);

		return $ret;
	}
}
