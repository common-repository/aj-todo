<?php
class AJTODO_ProjectTodoType{
	public $project;
	public $msg = "";
	public $todotype_colors = array(
		"#1d3cb8",
		"#bd0f0f",
		"#19912b",
		"#d8db30",
		"#42aaf5",
		"#144033",
		"#ad8c1d",
		"#5c5a54",
		"#73105d",
		"#6b2675",
		"#8ba390",
		"#516b69",
		"#6b504f",
	);

	public function __construct(){
	}

	private function setMessage(){
		$ret = "<div class='alert alert-info'>";
		$ret .= __("역할은 '프로젝트 멤버' 탭에서 생성할 수 있습니다.","ajtodo");
		$ret .= "</div>";
		return $ret;
	}

	public function todotypeListView(){
		$ret = "";
		$ret .= "<form id='ajtodo_form'>";
		$ret .= "<input type='hidden' name='dotype'>";
		$ret .= $this->createFormPart(
			__("할일 타입","ajtodo")
		);
		if(!$this->project->donedate){
			$ret .="<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=todotype&act=addtodotype&pid=".$this->project->id."' class='btn btn-info'>".__('할일 타입 추가', 'ajtodo' )."</a>";
		}
		$ret .= "</form>";
		return $ret;
	}

	private function getTodoCountByTodoType($key){
		global $wpdb;
		return $wpdb->get_var("select count(id) from ".AJTODO_DB_TODO."_".$this->project->id." where todotype = '".$key."'");
	}

	public function delTodoType($key){
		global $wpdb;
		$delindex = -1;
		for($i = 0; $i < count($this->project->todotype) ; $i++){
			if($this->project->todotype[$i]["key"] == $key){
				$delindex = $i;
			}
		}
		if($delindex != -1){
			array_splice($this->project->todotype, $delindex, 1);
		}
		$ret = $this->project->updateCols(
				array( "todotype" => json_encode($this->project->todotype, JSON_UNESCAPED_UNICODE))
			);

		if($ret)
			do_action('ajtodo_del_todotype', $this->project->id);

		return $ret;
	}

	public function createFormPart($title){
		$ret = "";
		$ret .= "<div class='fs18'>".$title."</div>";
		$ret .= "<table class='table ajtodo_members'>";
		$ret .= "<thead  class='thead-light'>";
		$ret .= "	<tr>";
		$ret .= "		<th scope='col' style='width:8px;'></th>";
		$ret .= "		<th scope='col' style='width:150px;'>".__("할일 타입 이름","ajtodo")."</th>";
		$ret .= "		<th scope='col' class='text-center'>".__("할일 개수","ajtodo")."</th>";
		$ret .= "		<th scope='col'>".__("기본 사용","ajtodo")."</th>";
		if(!$this->project->donedate){
			$ret .= "		<th scope='col' style='width:250px;'>".__("관리","ajtodo")."</th>";
		}
		$ret .= "	</tr>";
		$ret .= "</thead>";

		$ret .= "<tbody id='ajtodo_todotype_table'>";
		foreach($this->project->todotype as $todotype){
			$ret .= "<tr>";
			$ret .= "	<td scope='col'style='background:".$todotype["color"]."'></td>";
			$ret .= "	<td scope='col'>".$todotype["name"]."</td>";
			$ret .= "	<td scope='col' class='text-center' width=150>".$this->getTodoCountByTodoType($todotype["key"])."</td>";
			$ret .= "	<td scope='col'>".($todotype["default"] == "Y" ? __("기본","ajtodo") : __("","ajtodo"))."</td>";
			if(!$this->project->donedate){
				$ret .= "	<td scope='col'>";
				$ret .= "		<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=todotype&todotypekey=".$todotype["key"]."&act=addtodotype&pid=".$this->project->id."' class='btn btn-success btn-sm' id='ajtodo_edit_todotype'>".__('수정', 'ajtodo' )."</a>";
				if($todotype["default"] != "Y"){
					$ret .= "		<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=todotype&todotypekey=".$todotype["key"]."&act=del&pid=".$this->project->id."' class='btn btn-danger btn-sm' id='ajtodo_del_todotype'>".__('삭제', 'ajtodo' )."</a>";
				}
			}
			$ret .= "	</td>";
			$ret .= "<tr>";
		}
		$ret .= "</tbody>";
		$ret .= "</table>";
		return $ret;
	}

	private function getTodoTypeColors($todotypecolor){
		$ret = "<div id='ajtodo_color_box'>";
		$isnewset = true;
		foreach($this->todotype_colors as $color){
			if($todotypecolor == $color){
				$ret .= "<button class='fs18 btn btn-sm' style='background:".$color."' val='".$color."'>
					<i class='far fa-check-circle'></i></button>";
			}else{
				$ret .= "<button class='fs18 btn btn-sm' style='background:".$color."' val='".$color."'>";
				if($isnewset && !$todotypecolor){
					$ret .= "<i class='far fa-check-circle'></i>";
				}else{
					$ret .= "<i class='far fa-circle'></i>";
				}
				$ret .= "</button>";
				if(!$todotypecolor){ $isnewset = false; }
			}
		}
		$ret .= "</div>";
		return $ret;
	}

	public function getSingleTodoType($todotypekey){
		foreach($this->project->todotype as $s){
			if($s["key"] == $todotypekey){
				return $s;
			}
		}
	}

	public function addTodoTypeView($todotypekey){
		if($todotypekey){
			$todotype = $this->getSingleTodoType($todotypekey);
		}
		$defaulttodotype = $this->getDefaultTodoType();
		$ret = "";
		$ret .= "<form method='post' id='ajtodo_form'>";
		$ret .= "<input type='hidden' name='dotype' id='dotype' value='".($todotypekey ? "up" : "add")."'>";
		$ret .= "<input type='hidden' id='d_p_id' value='".$this->project->id."'>";
		$ret .= "<input type='hidden' id='ajtodo_todotype_color' name='ajtodo_todotype_color' value='".($todotypekey ? $todotype["color"] : "#ffffff")."'>";
		$ret .= wp_nonce_field('ajtodo_nonce','update_roleperm');

		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('할일 타입 Key', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option'>";
		if($todotypekey){
			$ret .= "<input type='hidden' class='form-control' 
				name='ajtodo_todotype_key' id='ajtodo_todotype_key' value='".$todotypekey."'>";
			$ret .= "<span class='font-italic'>".$todotypekey."</span>";
		}else{
			$ret .= "<input type='text' class='form-control form-control-sm' maxlength='10'
				name='ajtodo_todotype_key' id='ajtodo_todotype_key' style='width:150px'>";
			$ret .= "<p>".__("영문자와 숫자 그리고 일부 특수기호만 가능합니다.","ajtodo")."</p>";
		}
		$ret .= "	</div>";
		$ret .= "</div>";

		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('기본으로 사용', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-group'>";
		if($todotypekey && $todotype["default"] == "Y"){
			$ret .= "<input type='hidden' class='form-control' 
			name='ajtodo_todotype_default' id='ajtodo_todotype_default' value='Y'>";
			$ret .= "<span class='font-italic'>".__('이 상태를 기본으로 사용합니다.', 'ajtodo' )."</span>";
		}else{
			$ret .= "<input type='checkbox' style='margin-right:4px;' 
				name='ajtodo_todotype_default' id='ajtodo_todotype_default' value='Y'>";
			$ret .= "<label class='form-check-label' for='ajtodo_todotype_default'>".__('이 상태를 기본으로 사용합니다.', 'ajtodo' )."</label>";
			$ret .= " (".__("현재 기본 할일 타입 : ","ajtodo").$defaulttodotype["name"].")";
		}

		$ret .= "	</div>";
		$ret .= "</div>";

		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('타입 색상', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option'>";
		$ret .= $this->getTodoTypeColors($todotypekey ? $todotype["color"] : "");
		$ret .= "	</div>";
		$ret .= "</div>";

		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('할일 타입 이름', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option'>";
		$ret .= "<input type='text' class='form-control form-control-sm' 
			name='ajtodo_todotype_name' id='ajtodo_todotype_name' value='".($todotypekey ? $todotype["name"] : "")."' style='width:250px'>";
		$ret .= "	</div>";
		$ret .= "</div>";

		$ret .="<button id='ajtodobtn_save_todotype' class='btn btn-primary'>".__('변경 사항 적용', 'ajtodo' )."</button>";
		$ret .="<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=todotype&pid=".$this->project->id."' class='btn btn-info'>".__('할일 타입 목록', 'ajtodo' )."</a>";
		$ret .= "</form>";
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

	public function addTodoType($dotype, $todotypekey, $todotypename, $todotypecolor, $todotypedefault){
		if($todotypedefault){
			foreach($this->project->todotype as &$r){
				$r["default"] = "N";
			}
		}
		if($dotype == "add"){
			$has = false;
			foreach($this->project->todotype as $r){
				if($r["key"] == $todotypekey){
					$has = true;
				}
			}
			if($has){
				$this->msg = __("할일 타입 키가 중복됩니다. ","ajtodo");
				return false;
			}
			$this->project->todotype[] = array(
					"key" => $todotypekey, 
					"name" => $todotypename, 
					"default" => ($todotypedefault ? "Y" : "N"),
					"color" => $todotypecolor);
		}else{
			foreach($this->project->todotype as &$r){
				if($r["key"] == $todotypekey){
					$r["name"] = $todotypename;
					$r["color"] = $todotypecolor;
					$r["default"] = ($todotypedefault ? "Y" : "N");
				}
			}
		}
		$ret = $this->project->updateCol("todotype", json_encode($this->project->todotype, JSON_UNESCAPED_UNICODE));
		if($ret){
			if($dotype == "add"){
				do_action('ajtodo_add_todotype', $this->project->id, $todotypekey, $todotypename);
			}else{
				do_action('ajtodo_modify_todotype', $this->project->id, $todotypekey, $todotypename);
			}
		}
		
		return $ret;
	}

	public function getMembers(){
		$ret = array();
		foreach($this->project->roles as $role){
			$ret = array_unique(array_merge($ret, $role["users"]));
		}
		return $ret;
	}

	public function delConfirmView($todotypekey){
		$cate = $this->getSingleTodoType($todotypekey);
		$ret = "";
		$ret .= "<form method='post' id='ajtodo_form'>";
		$ret .= "<input type='hidden' name='dotype' id='dotype'>";
		$ret .= wp_nonce_field('ajtodo_nonce','update_roleperm');
		$ret .= "<div id='ajtodo_form'>
			<div class='alert alert-warning'>
				<div class='font-weight-bold fs18'>".__("할일 타입 이름","ajtodo")." : ".$cate["name"]."</div>
				<div>".__("이 할일 타입를 삭제하시겠습니까?","ajtodo")."</div>
				<ul class='mt16 fs14'>
					<li>".__("총 할일 개수","ajtodo")." : <b>".$this->getTodoCountByTodoType($todotypekey)."</b></li>
				</ul>
				<div>".__("할일 타입에 존재하는 할일들은 모두 '할일 타입 없음'이 됩니다.","ajtodo")."</div>
				<div>".__("할일 타입에 포함된 할일이 삭제되는 것은 아닙니다.","ajtodo")."</div>
			</div>
			<button class='btn btn-danger' id='btnDelTodoType'>".__("예, 삭제합니다.","ajtodo")."</button>
			<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=todotype&pid=".$this->project->id."' class='btn btn-primary'>".__("취소","ajtodo")."</a>
		</div>";
		$ret .= "</form>";
		return $ret;
	}
}
