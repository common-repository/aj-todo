<?php
class AJTODO_ProjectCategory{
	public $project;
	public $msg = "";

	public function __construct(){
	}

	private function setMessage(){
		$ret = "<div class='alert alert-info'>";
		$ret .= __("역할은 '프로젝트 멤버' 탭에서 생성할 수 있습니다.","ajtodo");
		$ret .= "</div>";
		return $ret;
	}

	public function categoryListView(){
		$ret = "";
		$ret .= "<form id='ajtodo_form'>";
		$ret .= "<input type='hidden' name='dotype'>";
		$ret .= $this->createFormPart(
			__("할일 카테고리","ajtodo")
		);
		if(!$this->project->donedate){
			$ret .="<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=category&act=addcategory&pid=".$this->project->id."' class='btn btn-info'>".__('카테고리 추가', 'ajtodo' )."</a>";
		}
		$ret .= "</form>";
		return $ret;
	}

	private function getTodoCountByCategory($key){
		global $wpdb;
		return $wpdb->get_var("select count(id) from ".AJTODO_DB_TODO."_".$this->project->id." where categorykey = '".$key."'");
	}

	public function delCategory($key){
		global $wpdb;
		$delindex = -1;
		for($i = 0; $i < count($this->project->category) ; $i++){
			if($this->project->category[$i]["key"] == $key){
				$delindex = $i;
			}
		}
		if($delindex != -1){
			array_splice($this->project->category, $delindex, 1);
		}
		$this->project->updateCols(
			array( "category" => json_encode($this->project->category, JSON_UNESCAPED_UNICODE))
		);

		$ret = $wpdb->query("update ".AJTODO_DB_TODO."_".$this->project->id." set categorykey = NULL where categorykey = '".$key."'");
		if($ret)
			do_action('ajtodo_del_category', $this->project->id); 

		return $ret;
	}

	public function createFormPart($title){
		$ret = "";
		$ret .= "<div class='fs18'>".$title."</div>";
		$ret .= "<table class='table ajtodo_members'>";
		$ret .= "<thead  class='thead-light'>";
		$ret .= "	<tr>";
		$ret .= "		<th scope='col' style='width:150px;'>".__("카테고리 이름","ajtodo")."</th>";
		$ret .= "		<th scope='col' class='text-center'>".__("할일 개수","ajtodo")."</th>";
		$ret .= "		<th scope='col'>".__("기본 담당자","ajtodo")."</th>";
		if(!$this->project->donedate){
			$ret .= "		<th scope='col' style='width:250px;'>".__("관리","ajtodo")."</th>";
		}
		$ret .= "	</tr>";
		$ret .= "</thead>";

		$ret .= "<tbody id='ajtodo_category_table'>";
		if(count($this->project->category) > 0){
			foreach($this->project->category as $category){
				$ret .= "<tr>";
				$ret .= "	<td scope='col'>".$category["name"]."</td>";
				$ret .= "	<td scope='col' width='150' class='text-center'>".$this->getTodoCountByCategory($category["key"])."</td>";
				$ret .= "	<td scope='col'>";
				if($category["leader"]){
					$ret .= 		AJTODO_ProjectMember::getUserSmallCard($category["leader"]);
				}else{
					$ret .= "<span class='font-italic'>".__("지정 안됨","ajtodo")."</span>";
				}
				$ret .= "		<div class='clearfix'></div>";
				$ret .= "	</td>";
				if(!$this->project->donedate){
					$ret .= "	<td scope='col'>";
					$ret .= "		<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=category&categorykey=".$category["key"]."&act=addcategory&pid=".$this->project->id."' class='btn btn-success btn-sm' id='ajtodo_edit_category'>".__('수정', 'ajtodo' )."</a>";
					$ret .= "		<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=category&categorykey=".$category["key"]."&act=del&pid=".$this->project->id."' class='btn btn-danger btn-sm' id='ajtodo_del_category'>".__('삭제', 'ajtodo' )."</a>";
					$ret .= "	</td>";
				}
				$ret .= "<tr>";
			}
		}else{
			$ret .= "<tr>";
			$ret .= "<td class='text-center' colspan=".(!$this->project->donedate ? "4" : "3").">".__("카테고리가 없습니다.","ajtodo")."</td>";
			$ret .= "<tr>";
		}
		$ret .= "</tbody>";
		$ret .= "</table>";
		return $ret;
	}

	public function getSingleCategory($categorykey){
		foreach($this->project->category as $s){
			if($s["key"] == $categorykey){
				return $s;
			}
		}
	}

	public function addCategoryView($categorykey){
		if($categorykey){
			$category = $this->getSingleCategory($categorykey);
		}
		$ret = "";
		$ret .= "<form method='post' id='ajtodo_form'>";
		$ret .= "<input type='hidden' id='dotype' name='dotype' value='".($categorykey ? "up" : "add")."'>";
		$ret .= "<input type='hidden' id='d_p_id' value='".$this->project->id."'>";
		$ret .= "<input type='hidden' id='ajtodo_category_color' name='ajtodo_category_color' value='".($categorykey ? $category["color"] : "#ffffff")."'>";
		$ret .= wp_nonce_field('ajtodo_nonce','update_roleperm');

		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('카테고리 Key', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option'>";
		if($categorykey){
			$ret .= "<input type='hidden' class='form-control'
				name='ajtodo_category_key' id='ajtodo_category_key' value='".$categorykey."'>";
			$ret .= "<span class='font-italic'>".$categorykey."</span>";
		}else{
			$ret .= "<input type='text' class='form-control form-control-sm' maxlength='10'
				name='ajtodo_category_key' id='ajtodo_category_key' style='width:150px'>";
			$ret .= "<p>".__("10자 이내의 영문자와 숫자만 가능합니다.","ajtodo")."</p>";
		}
		$ret .= "	</div>";
		$ret .= "</div>";

		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('카테고리 이름', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option'>";
		$ret .= "<input type='text' class='form-control form-control-sm' 
			name='ajtodo_category_name' id='ajtodo_category_name' value='".($categorykey ? $category["name"] : "")."' style='width:250px'>";
		$ret .= "	</div>";
		$ret .= "</div>";

		$ret .= "<div class='form-group'>";
		$ret .= "	<label class='form-title'>".__('기본 담당자', 'ajtodo' )."</label>";
		$ret .= "	<div class='form-option'>";
		$ret .= "		<select name='ajtodo_category_leader'>";
		if($categorykey){
			$ret .= "			<option value='' ".($category["leader"] == "" ? "selected" : "").">".__("지정 안함","ajtodo")."</option>";
			foreach($this->getMembers() as $member){
				$ret .= "		<option value='".$member."' ".($category["leader"] == $member ? "selected" : "").">".get_userdata($member)->display_name."</option>";
			}
		}else{
			$ret .= "			<option value=''>".__("지정 안함","ajtodo")."</option>";
			foreach($this->getMembers() as $member){
				$ret .= "		<option value='".$member."'>".get_userdata($member)->display_name."</option>";
			}
		}
		$ret .= "		</select>";
		$ret .= "		<span>".__("이 카테고리에 등록될 경우, 자동으로 담당자에게 할일이 지정됩니다.","ajtodo")."</span>";
		$ret .= "	</div>";
		$ret .= "</div>";

		$ret .="<button id='ajtodobtn_save_category' class='btn btn-primary'>".__('변경 사항 적용', 'ajtodo' )."</button>";
		$ret .="<button id='ajtodobtn_cancel_category' class='btn btn-info'>".__('카테고리 목록', 'ajtodo' )."</button>";
		$ret .= "</form>";
		return $ret;
	}

	public function addCategory($dotype, $categorykey, $categoryname, $categorycolor, $categoryleader){
		if($dotype == "add"){
			$has = false;
			foreach($this->project->category as $r){
				if($r["key"] == $categorykey){
					$has = true;
				}
			}
			if($has){
				$this->msg = __("카테고리 키가 중복됩니다. ","ajtodo");
				return false;
			}
			$this->project->category[] = array(
					"key" => $categorykey, 
					"name" => $categoryname, 
					"leader" => $categoryleader, 
					"color" => $categorycolor);
		}else{
			foreach($this->project->category as &$r){
				if($r["key"] == $categorykey){
					$r["name"] = $categoryname;
					$r["leader"] = $categoryleader;
					$r["color"] = $categorycolor;
				}
			}
		}
		
		$ret = $this->project->updateCol("category", json_encode($this->project->category, JSON_UNESCAPED_UNICODE));
		if($ret){
			if($dotype == "add"){
				do_action('ajtodo_add_category', $this->project->id, $categorykey, $categoryname); 
			}else{
				do_action('ajtodo_modify_category', $this->project->id, $categorykey, $categoryname);
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

	public function delConfirmView($categorykey){
		$cate = $this->getSingleCategory($categorykey);
		$ret = "";
		$ret .= "<form method='post' id='ajtodo_form'>";
		$ret .= "<input type='hidden' name='dotype' id='dotype'>";
		$ret .= wp_nonce_field('ajtodo_nonce','update_roleperm');
		$ret .= "<div id='ajtodo_form'>
			<div class='alert alert-warning'>
				<div class='font-weight-bold fs18'>".__("카테고리 이름","ajtodo")." : ".$cate["name"]."</div>
				<div>".__("이 카테고리를 삭제하시겠습니까?","ajtodo")."</div>
				<ul class='mt16 fs14'>
					<li>".__("총 할일 개수","ajtodo")." : <b>".$this->getTodoCountByCategory($categorykey)."</b></li>
				</ul>
				<div>".__("카테고리에 존재하는 할일들은 모두 '카테고리 없음'이 됩니다.","ajtodo")."</div>
				<div>".__("카테고리에 포함된 할일이 삭제되는 것은 아닙니다.","ajtodo")."</div>
			</div>
			<button class='btn btn-danger' id='btnDelCategory'>".__("예, 삭제합니다.","ajtodo")."</button>
			<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=category&pid=".$this->project->id."' class='btn btn-primary'>".__("취소","ajtodo")."</a>
		</div>";
		$ret .= "</form>";
		return $ret;
	}
}
