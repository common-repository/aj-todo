<?php
class AJTODO_ProjectStatus{
	public $project;
	public $msg = "";
	public $project_basic_perms = array();
	public $project_todo_perms = array();
	public $status_icons = array(
		"angry",
		"arrow-alt-circle-down",
		"arrow-alt-circle-left",
		"arrow-alt-circle-right",
		"arrow-alt-circle-up",
		"bell",
		"bookmark",
		"calendar-alt",
		"calendar-check",
		"check-circle",
		"check-square",
		"caret-square-right",
		"circle",
		"clock",
		"comment-alt",
		"flag",
		"grin",
		"grin-wink",
		"heart",
		"hourglass",
		"laugh-wink",
		"meh",
		"paper-plane",
		"square",
		"stop-circle"
	);

	public function __construct(){
	}

	public function statusListView(){
		//$this->project->updateCol("statuses", json_encode(AJTODO_ProjectDefault::DefaultStatus(), JSON_UNESCAPED_UNICODE));
		$ret = "";
		$ret .= "<form id='ajtodo_form'>";
		$ret .= "<input type='hidden' name='dotype'>";
		$ret .= $this->createFormPart( __("상태 목록","ajtodo"));
		if(!$this->project->donedate){
			$ret .="<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=status&act=addstatus&pid=".$this->project->id."' class='btn btn-info'>".__('상태 추가', 'ajtodo' )."</a>";
		}
		$ret .= "</form>";
		return $ret;
	}

	private function getUserSmallCard($id){
		$user = get_userdata($id);
		$ret = "<table class='ajtodo_small_usercard float-left'><tr>";
		$ret .= "<td>".get_avatar($user->ID, 18)."</td>";
		$ret .= "<td style='padding-right:8px !important;'>".$user->display_name."</td>";
		$ret .= "</tr></table>";
		return $ret;
	}

	public function getStatusTypeNameByKey($statustypekey){
		switch($statustypekey){
			case "S" : return __("작업전 상태","ajtodo");
			case "I" : return __("작업중 상태","ajtodo");
			case "D" : return __("작업완료 상태","ajtodo");
		}
		return $statustypekey;
	}

	public function createFormPart($title){
		$ret = "";
		$ret .= "<div class='fs18'>".$title."</div>";
		$ret .= "<table class='table ajtodo_members'>";
		$ret .= "<thead  class='thead-light'>";
		$ret .= "	<tr>";
		$ret .= "		<th scope='col' style='width:150px;'>".__("상태","ajtodo")."</th>";
		$ret .= "		<th scope='col' style='width:120px;'>".__("상태 범주","ajtodo")."</th>";
		$ret .= "		<th scope='col'>".__("상태 변경 및 가능한 역할","ajtodo")."</th>";
		if(!$this->project->donedate){
			$ret .= "		<th scope='col' style='width:250px;'>".__("관리","ajtodo")."</th>";
		}
		$ret .= "	</tr>";
		$ret .= "</thead>";
		$ret .= "<tbody id='ajtodo_role_table'>";
		foreach($this->project->statuses as $stat){
			$ret .= "<tr>";
			$ret .= "	<td scope='col'><i class='far ".$stat["icon"]."'></i>".$stat["name"]."</td>";
			$ret .= "	<td scope='col'>".$this->getStatusTypeNameByKey($stat["statustype"])."</td>";
			$ret .= "	<td scope='col'>";
			$ret .= $this->viewRules($stat);
			//foreach($this->getUsersByRole($status["key"]) as $mem){
			//	$ret .= $this->getUserSmallCard($mem);
			//}
			$ret .=	"	<div class='clearfix'></div>";
			$ret .=	"	</td>";
			if(!$this->project->donedate){
				$ret .= "	<td scope='col'>";
				$ret .= "		<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=status&act=addrule&statuskey=".$stat["key"]."&pid=".$this->project->id."' class='btn btn-success'>".__('규칙 관리', 'ajtodo' )."</a>";
				$ret .= "		<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=status&act=addstatus&statuskey=".$stat["key"]."&pid=".$this->project->id."' class='btn btn-info'>".__('상태 관리', 'ajtodo' )."</a>";
			}
			$ret .= "	</td>";
			$ret .= "<tr>";
		}
		$ret .= "</tbody>";
		$ret .= "</table>";
		return $ret;
	}

	public function viewRules($stat){
		$ret = "";
		foreach($stat["rules"] as $rule){
			$ret .= "<ul class='list-group ajtodo_statusrole' style='margin-bottom:8px;'>";
			$statTo = $this->getSingleStatus($rule["to"]);
			$ret .= "<li class='list-group-item'>";
			$ret .= "<i class='fas fa-arrow-right'></i>";
			$ret .= "<i class='far ".$statTo["icon"]."'></i>";
			$ret .= $statTo["name"];
			$ret .= "</li>";
			$ret .= "<li class='list-group-item'>";
			foreach($rule["roles"] as $role){
				$ret .= "<button type='button' class='disabled btn btn-secondary'>".$this->getRoleNameByKey($role)."</button>";
			}
			$ret .= "</li>";
			$ret .= "</ul>";
		}
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

	public function delStatus($statuskey, $tostatuskey){
		$n = -1;
		for($i = 0; $i <count($this->project->statuses); $i++){
			if($this->project->statuses[$i]["key"] == $statuskey){
				$n = $i;
			}
		}
		if($n > -1){
			array_splice($this->project->statuses, $n, 1);
			for($i = 0; $i <count($this->project->statuses); $i++){
				$m = -1;
				for($j = 0; $j <count($this->project->statuses[$i]["rules"]); $j++){
					if($this->project->statuses[$i]["rules"][$j]["to"] == $statuskey){
						$m = $j;
					}
				}
				if($m > -1){
					array_splice($this->project->statuses[$i]["rules"], $m, 1);
				}
			}

			$arrdo = array("statuskey" => $tostatuskey);
			if($tostatuskey == "closed")
				$arrdo["donedated"] = date("Y-m-d H:i:s");
			if($tostatuskey == "open")
				$arrdo["donedated"] = "";

			$this->project->updateTodo($arrdo, "statuskey", $statuskey);
			$this->project->updateCol("statuses", 
				json_encode($this->project->statuses, JSON_UNESCAPED_UNICODE));
		}
	}

	public function addStatus($dotype, $statuskey, $statusname, $statusicon, $statustype){
		if($dotype == "add"){
			$has = false;
			foreach($this->project->statuses as $r){
				if($r["key"] == $statuskey){
					$has = true;
				}
			}
			if($has){
				$this->msg = __("상태 키가 중복됩니다. ","ajtodo");
				return false;
			}
			$this->project->statuses[] = array(
					"key" => $statuskey, 
					"name" => $statusname, 
					"statustype" => $statustype, 
					"icon" => $statusicon,
					"rules" => array(),
					"color" => "primary");
		}else{
			foreach($this->project->statuses as &$r){
				if($r["key"] == $statuskey){
					$r["name"] = $statusname;
					$r["icon"] = $statusicon;
				}
			}
		}
		return $this->project->updateCol("statuses", 
				json_encode($this->project->statuses, JSON_UNESCAPED_UNICODE));
	}

	private function getStatusNameByKey($statuskey){
		$ret = "";
		if($statuskey){
			foreach($this->project->statuses as $s){
				if($s["key"] == $statuskey){
					$ret = $s["name"];
				}
			}
		}
		return $ret;
	}

	public static function getStatusByType($prj, $type){
		foreach($prj->statuses as $s){
			if($s["statustype"] == $type){
				return $s;
			}
		}
	}


	public static function getSingleStatusByStatus($statuses, $statuskey){
		foreach($statuses as $s){
			if($s["statustype"] == $statuskey){
				return $s;
			}
		}
	}

	public function getSingleStatus($statuskey){
		foreach($this->project->statuses as $s){
			if($s["key"] == $statuskey){
				return $s;
			}
		}
	}

	private function getStatusIcon($staticon){
		$ret = "<div id='ajtodo_icon_box'>";
		$isnewset = true;
		foreach($this->status_icons as $icon){
			if($staticon == "fa-".$icon){
				$ret .= "<button class='fs18 btn btn-sm btn-primary' val='fa-".$icon."'>
					<i class='far fa-".$icon."'></i></button>";
			}else{
				$ret .= "<button class='fs18 btn btn-sm ".($isnewset && !$staticon ? "btn-primary" : "")."' val='fa-".$icon."'>
					<i class='far fa-".$icon."'></i></button>";
				if(!$staticon){ $isnewset = false; }
			}
		}
		$ret .= "</div>";
		return $ret;
	}

	public function addStatusView($statuskey){
		if($statuskey){
			$stat = $this->getSingleStatus($statuskey);
		}
		$ret = "";
		$ret .= "<div class='fs18 mb8'>";
		if($statuskey){
			$ret .= __("프로젝트 상태 수정","ajtodo");
		}else{
			$ret .= __("프로젝트 상태 추가","ajtodo");
		}
		$ret .= "</div>";
		$ret .= "<form id='ajtodo_form' method='post'>";
		$ret .= "<input type='hidden' id='dotype' name='dotype' value='".($statuskey ? "update" : "add")."'>";
		$ret .= "<input type='hidden' id='ajtodo_status_icon' name='ajtodo_status_icon' value='".($statuskey ? $stat["icon"] : "")."'>";

		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('상태 아이콘', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option'>";
		$ret .= $this->getStatusIcon($statuskey ? $stat["icon"] : "");
		$ret .= "	</div>";
		$ret .= "</div>";
		
		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('상태 Key', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option'>";
		if($statuskey){
			$ret .= "<input type='hidden' class='form-control' 
				name='ajtodo_status_key' id='ajtodo_status_key' value='".$statuskey."'>";
			$ret .= "<span class='font-italic'>".$statuskey."</span>";
		}else{
			$ret .= "<input type='text' class='form-control form-control-sm' maxlength='10' 
				name='ajtodo_status_key' id='ajtodo_status_key' style='width:150px'>";
			$ret .= "<p>".__("10자 이내의 영문자와 숫자만 가능합니다.","ajtodo")."</p>";
		}
		$ret .= "	</div>";
		$ret .= "</div>";
		
		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('상태 범주', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option'>";
		if($statuskey){
			$ret .= "<input type='hidden' class='form-control' 
				name='ajtodo_status_type' id='ajtodo_status_type' value='".$statuskey."'>";
			$ret .= "<span class='font-italic'>".$this->getStatusTypeNameByKey($stat["statustype"])."</span>";
		}else{
			$ret .= "<input type='hidden' class='form-control' 
				name='ajtodo_status_type' id='ajtodo_status_type' value='I'>";
			$ret .= "<span class='font-italic'>".$this->getStatusTypeNameByKey("I")."</span>";

			//$ret .= "<select class='input-control' 
			//	name='ajtodo_status_type' id='ajtodo_status_type' style='width:250px'>";
			//$ret .= "<option value='S'>".$this->getStatusTypeNameByKey("S")."</option>"; 
			//$ret .= "<option value='I'>".$this->getStatusTypeNameByKey("I")."</option>"; 
			//$ret .= "<option value='D'>".$this->getStatusTypeNameByKey("D")."</option>"; 
			//$ret .= "</select>"; 
		}
		$ret .= "	</div>";
		$ret .= "</div>";

		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('상태 이름', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option'>";
		$ret .= "<input type='text' class='form-control form-control-sm' 
			name='ajtodo_status_name' id='ajtodo_status_name' value='".($statuskey ? $stat["name"] : "")."' style='width:250px'>";
		$ret .= "	</div>";
		$ret .= "</div>";

		$ret .= "<a href='#' id='ajtodo_update_role' class='btn btn-primary'>".__('변경 사항 적용', 'ajtodo' )."</a>";
		if($statuskey){
			if($statuskey != "open" && $statuskey != "closed"){
				$ret .= "<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=status&act=del&statuskey=".$statuskey."&pid=".$this->project->id."' class='btn btn-danger'>".__('상태 삭제', 'ajtodo' )."</a>";
			}
		}
		$ret .= "<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=status&pid=".$this->project->id."' class='btn btn-success'>".__('상태 목록', 'ajtodo' )."</a>";
		$ret .= "</form>";
		return $ret;
	}

	public function addRuleView($statuskey){
		$stat = $this->getSingleStatus($statuskey);
		$ret = "";
		$ret .= "<form id='ajtodo_form' method='post'>";
		$ret .= "<input type='hidden' name='dotype' value='up'>";
		$ret .= "<input type='hidden' name='actionrole' id='actionrole'>";
		$ret .= $this->createRulePart($stat);
		$ret .= "<a href='#' id='ajtodo_update_statusrole' class='btn btn-primary'>".__('변경 사항 적용', 'ajtodo' )."</a>";
		$ret .= "<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=status&id=".$this->project->id."' class='btn btn-info'>".__('상태 목록', 'ajtodo' )."</a>";
		$ret .= "</form>";
		return $ret;
	}

	public function createRulePart($stat){
		$ret = "";
		$ret .= "<div class='fs18'>'".$stat["name"]."'". __(" - 규칙 관리","ajtodo")."</div>";
		$ret .= "<table class='table ajtodo_members'>";
		$ret .= "<thead  class='thead-light'>";
		$ret .= "	<tr>";
		//$ret .= "		<th scope='col' style='width:20%;max-width:250px;'>".__("상태","ajtodo")."</th>";
		$ret .= "		<th scope='col' style='width:150px;'>".__("상태 변경","ajtodo")."</th>";
		$ret .= "		<th scope='col' style='width:250px;'>".__("상태 변경이 가능한 역할","ajtodo")."</th>";
		$ret .= "		<th scope='col' style=''>".__("역할 추가","ajtodo")."</th>";
		$ret .= "	</tr>";
		$ret .= "</thead>";
		$ret .= "<tbody id='ajtodo_rule_table'>";
		foreach($this->project->statuses as $status){
			if($status["key"] == $stat["key"])
				continue;
			$ret .= "<tr>";
			$ret .= "	<td scope='col'>".$stat["name"]." -> ".$status["name"]."</td>";
			$ret .= "	<td scope='col'>";
			$ret .= "		<div class='input-group input-group-sm'>";
			$ret .= "			<select class='ajtodo_sel_role' val='".$status["key"]."'>";
			foreach($this->project->roles as $role){
				$ret .= "		<option value='".$role["key"]."'>".$role["name"]."</option>";
			}
			$ret .= "			</select>";
			$ret .= "			<div class='input-group-append'>";
			$ret .= "				<button class='btnaddroletostatus btn btn-primary' val='".$status["key"]."'>".__('추가', 'ajtodo' )."</button>";
			$ret .= "			</div>";
			$ret .= "		</div>";
			$ret .= "	</td>";
			$ret .= "	<td scope='col' class='statusrolebox' val='".$status["key"]."'>";
			foreach($stat["rules"] as $rule){
				if($rule["to"] == $status["key"]){
					foreach($rule["roles"] as $rolekey){
						$ret .= $this->makeRole($rolekey, $this->getRoleNameByKey($rolekey));
					}
				}
			}
			$ret .="	</td>";
			$ret .= "</tr>";
		}
		$ret .= "</tbody>";
		$ret .= "</table>";
		return $ret;
	}

	private function getRoleNameByKey($key){
		foreach($this->project->roles as $role){
			if($role["key"] == $key)
				return $role["name"];
		}
		return "";
	}

	private function getTodoCountByStatus($key){
		global $wpdb;
		return $wpdb->get_var("select count(id) from ".AJTODO_DB_TODO."_".$this->project->id." where statuskey = '".$key."'");
	}

	public function delConfirmView($statuskey){
		$status = $this->getSingleStatus($statuskey);
		$scount = $this->getTodoCountByStatus($statuskey);
		$ret = "";
		$ret .= "<form method='post' id='ajtodo_form'>";
		$ret .= "<input type='hidden' name='dotype' id='dotype' value='del'>";
		$ret .= wp_nonce_field('ajtodo_nonce','update_roleperm');
		$ret .= "<div id='ajtodo_form'>
			<div class='alert alert-warning'>
				<div class='font-weight-bold fs18'>".__("상태 이름","ajtodo")." : ".$status["name"]."</div>
				<div>".__("이 상태를 삭제하시겠습니까?","ajtodo")."</div>
				<ul class='mt16 fs14'>
					<li>".__("이 상태의 할일 개수","ajtodo")." : <b>".$this->getTodoCountByStatus($statuskey)."</b></li>
				</ul>";
		if($scount){
			$ret .= "<div>";
			$ret .= "	<div class=''>".__("이 상태의 할일들을 어떤 상태로 변경할까요?","ajtodo")."</div>";
			$ret .= "	<div><select name='tostatuskey'>";
			foreach($this->project->statuses as &$r){
				if($r["key"] != $statuskey){
					$ret .= "	<option value='".$r["key"]."'>".$r["name"]."</option>";
				}
			}
			$ret .= "	</select></div>";
			$ret .= "</div>";
		}
		$ret .= "</div>
			<button class='btn btn-danger'>".__("예, 삭제합니다.","ajtodo")."</button>
			<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=status&pid=".$this->project->id."' class='btn btn-primary'>".__("취소","ajtodo")."</a>
		</div>";
		$ret .= "</form>";
		return $ret;
	}

	public function makeRole($key, $name){
		$rolebox = "";
		if($name){
			$rolebox = "<div class='btn-group' role='group' val=".$key.">";
			$rolebox .= "<button type='button' class='disabled btn btn-secondary'>".$name."</button>";
			$rolebox .= "<button type='button' class='btn btn-danger'><i class='far fa-minus-square'></i></button>";
			$rolebox .= "</div>";
		}
		return $rolebox;
	}

	public function updateActionRole($statuskey, $actionrole){
		foreach($this->project->statuses as &$r){
			if($r["key"] == $statuskey){
				$r["rules"] = json_decode(stripslashes($actionrole), true);
			}
		}
		return $this->project->updateCol("statuses", 
				json_encode($this->project->statuses, JSON_UNESCAPED_UNICODE));
	}
}
