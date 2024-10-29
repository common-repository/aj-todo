<?php
class AJTODO_ProjectPlan{
	public $project;
	public $id;
	public $msg;

	private $listwidth = 280;

	public function __construct(){
	}

	public function getTodoModal(){
		return "
		<div class='modal fade' id='todpopview' tabindex='-1' role='dialog' aria-hidden='true'>
		  <div class='modal-dialog modal-lg' role='document' style='max-width: 920px !important;'>
			<div class='modal-content todo_singleview' style='padding: 18px;'>
			  <div class='modal-header' style='border:0px'>
		<div class='clearfix' style='width: 100%;'>
			<div class='float-left' style='width:80%'>
				<div>
					<span class='badge badge-primary fs12' id='ajtodo_sv_tkey' style='padding:4px 8px;'></span>
					<span class='badge fs12' id='ajtodo_sv_status' style='padding:4px 8px;'></span>
					<span class='badge badge-secondary' style='padding:4px;'><i class='fas fa-external-link-alt'></i> <a id='ajtodo_sv_todolink' style='color:#eee;margin:0;'></a></span>
				</div>
				<div class='font-weight-bold fs18' style='margin-top:4px;margin-bottom:0px' id='ajtodo_sv_title'></div>
				<div>
					<span class='fs12' style='margin-top:4px;margin-bottom:0px;margin-right:8px;' id='ajtodo_sv_regdate'></span>
					<span class='fs12' style='margin-top:4px;margin-bottom:0px' id='ajtodo_sv_donedate'></span>
				</div>
			</div>
			<button class='btn btn-sm btn-info float-right' id='ajtodo_sv_close'>".__("닫기","ajtodo")."</button>
		</div>
			  </div>
			  <div class='modal-body'>
	<div class='' val=''>
		<div id='ajtodo_vd_actionbox'>
			<hr style='margin-top: 4px;'/>
			<div class='' id='ajtodo_sv_action'></div>
		</div>
		<hr/>
		<table>
			<tr>
				<td style='padding:0px;padding-right:24px;' class='isonlyteam'>
					<div class='ajtodo_todo_view_item'>
						<p style='margin-bottom:0px;'><i class='fas fa-hammer'></i> ".__("담당자","ajtodo")."</p>
						<div class='font-weight-bold' id='ajtodo_sv_assignee'></div>
					</div>
				</td>
				<td style='padding:0px;padding-right:24px;' class='isonlyteam'>
					<div class='ajtodo_todo_view_item'>
						<p style='margin-bottom:0px;'><i class='fas fa-pen-alt'></i> ".__("작성자","ajtodo")."</p>
						<div class='font-weight-bold' id='ajtodo_sv_author'></div>
					</div>
				</td>
				<td style='padding:0px;padding-right:24px;'>
					<div class='ajtodo_todo_view_item'>
						<p style='margin-bottom:0px;'><i class='fas fa-vote-yea'></i> ".__("플랜","ajtodo")."</p>
						<div class='font-weight-bold' id='ajtodo_sv_plan'></div>
					</div>
				</td>
				<td style='padding:0px;padding-right:24px;'>
					<div class='ajtodo_todo_view_item'>
						<p style='margin-bottom:0px;'><i class='fas fa-folder'></i> ".__("카테고리","ajtodo")."</p>
						<div class='font-weight-bold' id='ajtodo_sv_category'></div>
					</div>
				</td>
				<td style='padding:0px;padding-right:24px;'>
					<div class='ajtodo_todo_view_item'>
						<p style='margin-bottom:0px;'><i class='fas fa-vote-yea'></i> ".__("이슈 타입","ajtodo")."</p>
						<div class='font-weight-bold' id='ajtodo_sv_todotype'></div>
					</div>
				</td>
			</tr>
		</table>
		<hr/>
		<ul class='list-group ajtodo_doc_list' id='ajtodo_doc_list_dv' style='padding-left:4px !important;'></ul>
		<hr id='ajtodo_doc_list_dv_hr' style='margin: 8px 0px !important;'/>
		<div id='ajtodo_content' class='ajtodo_todo_view_item'></div>
	</div>
			  </div>
			</div>
		  </div>
		</div>";
	}

	public function setPlanBoard($orderlist){
		$maxwidth = count($this->project->statuses) * ($this->listwidth + 8);

		$addbox = "<div>";
		$addbox .= "<div id='ajtodo_todotitle_add'><i class='fas fa-plus'></i> ".__("할일 추가","ajtodo")."</div>";
		$addbox .= "<div id='ajtodo_todotitle_input' style='padding:4px;display:none;'>";
		$addbox .= "<input type='text' class='' style='border-radius:4px !important;width:100%;' "; 
		$addbox .= " id='ajtodo_todotitle' ";
		$addbox .= " placeholder='".__("할일을 입력해보세요.","ajtodo")."'></div>";
		$addbox .= "</div>";

		$ret = "<div class='ajtodo1-board fs14' id='aj_board_main' style='min-width:".$maxwidth."px;position:relative;'>";
		foreach($this->project->statuses as $status){
			if($status['key'] == "open"){
				$ret .= "<div class='ajtodo-board-column ".$status['key']."' style='background:#403f45;color:#fff;'>";
				$ret .= "<div class='ajtodo-board-column-header'><i class='far ".$status['icon']."'></i> ".$status['name']."</div>";
				$ret .= "<ul class='ajtodo-board-column-content' var='".$status['key']."' id='aj_status_".$status['key']."'>";
				$ret .= "</ul>";
				$ret .= $addbox;
				$ret .= "</div>";
			}
		}
		$ret .= "<div class='ajtodo-list-dock'>";
		$sarr = array();
		if($orderlist){
			$sarr = json_decode($orderlist, true);
			$arrset = array();
			foreach($sarr as $a){
				if($a['status'] != "open" && $a['status'] != "closed"){
					foreach($this->project->statuses as $status){
						if($status['key'] == $a['status']){
							$arrset[] = $a['status'];
							$ret .= "<div class='ajtodo-board-column ajtodo_status_movable ".$status['key']."' style='background:#3e7ab8;color:#fff;'>";
							$ret .= "<div class='ajtodo-board-column-header'><i class='far ".$status['icon']."'></i> ".$status['name']."</div>";
							$ret .= "<ul class='ajtodo-board-column-content' var='".$status['key']."' id='aj_status_".$status['key']."'>";
							$ret .= "</ul>";
							$ret .= "</div>";
						}
					}
				}
			}
			foreach($this->project->statuses as $status){
				if(!in_array($status['key'], $arrset) && $status['key'] != "open" && $status['key'] != "closed"){
					$ret .= "<div class='ajtodo-board-column ajtodo_status_movable ".$status['key']."' style='background:#3e7ab8;color:#fff;'>";
					$ret .= "<div class='ajtodo-board-column-header'><i class='far ".$status['icon']."'></i> ".$status['name']."</div>";
					$ret .= "<ul class='ajtodo-board-column-content' var='".$status['key']."' id='aj_status_".$status['key']."'>";
					$ret .= "</ul>";
					$ret .= "</div>";
				}
			}
		}else{
			foreach($this->project->statuses as $status){
				if($status['key'] != "open" && $status['key'] != "closed"){
					$ret .= "<div class='ajtodo-board-column ajtodo_status_movable ".$status['key']."' style='background:#3e7ab8;color:#fff;'>";
					$ret .= "<div class='ajtodo-board-column-header'><i class='far ".$status['icon']."'></i> ".$status['name']."</div>";
					$ret .= "<ul class='ajtodo-board-column-content' var='".$status['key']."' id='aj_status_".$status['key']."'>";
					$ret .= "</ul>";
					$ret .= "</div>";
				}
			}
		}
		$ret .= "</div>";
		foreach($this->project->statuses as $status){
			if($status['key'] == "closed"){
				$ret .= "<div class='ajtodo-board-column ".$status['key']."' style='background:#67956d;color:#fff;'>";
				$ret .= "<div class='ajtodo-board-column-header'><i class='far ".$status['icon']."'></i> ".$status['name']."</div>";
				$ret .= "<ul class='ajtodo-board-column-content' var='".$status['key']."' id='aj_status_".$status['key']."'>";
				$ret .= "</ul>";
				$ret .= "</div>";
			}
		}
		$ret .= "</div>";
		return $ret;
	}

	public function planListViewAjax($todolist){
		$ret = "";
		$ret .= "<div id='ajtodo_plan' class='row'>";
		$ret .= "	<div class='col' style='max-width: 240px;padding-right:0px;'>";
		if($this->project->hasPerm("tp_plan_create") && !$this->project->donedate){
			$ret .= "		<div class='btn-group mb8' style='width:100%'>";
			$ret .= "			<button type='button' id='addplan' class='btn btn-primary' style='width:100%;margin-right:0px;'>".__("플랜 만들기","ajtodo")."</a>";
			$ret .= "			<button type='button' class='btn btn-secondary' id='tglplan' style='width:32px;padding: 0px 8px;margin-right:0px;'>";
			$ret .=	"				<i class='far fa-eye-slash'></i>";
			$ret .=	"			</button>";
			$ret .= "		</div>";
		}else{
			$ret .= "		<button type='button' class='btn btn-secondary mb8' id='tglplan' style='width:100%;'>";
			$ret .=	"			<i class='far fa-eye-slash'></i> ".__("완료된 플랜 제외","ajtodo");
			$ret .=	"		</button>";
		}
		$ret .= "		<ul class='planboxlist' id='aj_planlist'>";
		$ret .= "		</ul>";
		$ret .= "	</div>";
		$ret .= "	<div class='col'>";
		//$ret .= " 	<ul class='nav nav-tabs' style='    margin-bottom: 4px;'>";
		//$ret .= "		<li class='nav-item'>";
		//$ret .= "			<a class='nav-link active' href='#' id='aj_plan_todo_list'>". __("할일", "ajtodo")."</a>";
		//$ret .= "		</li>";
		//$ret .= "		<li class='nav-item'>";
		//$ret .= "			<a class='nav-link' href='#' id='aj_plan_report'>". __("리포트", "ajtodo")."</a>";
		//$ret .= "		</li>";
		//$ret .= "		<li class='nav-item'>";
		//$ret .= "			<a class='nav-link' href='#' id='aj_plan_worker'>". __("작업자", "ajtodo")."</a>";
		//$ret .= "		</li>";
		//$ret .= " 	</ul>";
		$ret .= 	$todolist;
		$ret .= "	</div>";
		$ret .= "</div>";
		return $ret;
	}

	public function planListView(){
		$ret = "";
		$ret .= "<form id='ajtodo_form'>";
		$ret .= "<input type='hidden' name='dotype'>";
		$ret .= $this->createFormPart(
			__("진행중/진행예정 플랜","ajtodo"), false
		);
		$ret .="<div class='mt24 mb24'><hr/></div>";
		$ret .= $this->createFormPart(
			__("완료된 플랜","ajtodo"), true
		);
		$ret .= "</form>";
		return $ret;
	}

	public function showPlanInfo($planid = ""){
		$ret = "";
		foreach($this->project->plan as $plan){
			if($planid && ($planid != $plan["id"])){
				continue;
			}
			$isdone = $plan["donedate"] != "";
			$ret .= "<div class='card ";
			$ret .= " ".($plan["ising"] == "Y" ? " bg-info text-white " : "");
			if($plan["donedate"]){
				$ret .= " text-white bg-dark ";
			}
			$ret .= "'>";
			$ret .= "	<div class='card-header'>".$plan["plantitle"];
			if($plan["ising"] == "Y"){
				$ret .= " <span class='badge badge-light'>".__("진행중","ajtodo")."</span>";
			}
			$ret .= "		<span class='badge badge-secondary float-right' style='margin-top: 2px;'>";
			$ret .= 		__("할일 개수","ajtodo")." : ".$this->getTodoCountByPlan($plan["id"])."</span>";
			$ret .=	"	</div>";
			$ret .= "	<div class='card-body'>";
			if(!$plan["donedate"]){
				$ret .= "		".$this->getPlanProgress2($plan["id"])."";
			}

			$ret .= "		<p class='card-text' style='margin-bottom: 8px;'>".__("시작일")." : ".substr($plan["startdate"],0, 10);
			if($plan["donedate"]){
				$ret .= " ~ ".__("완료일")." : ".substr($plan["donedate"],0, 10)." (".__("목표일")." : ".substr($plan["finishdate"],0, 10).")";
			}else{
				$ret .= " ~ ".__("목표일")." : ".substr($plan["finishdate"],0, 10);
			}
			$ret .= "		</p>";

			$ret .= "		<p class='card-text font-weight-bold' style='margin-bottom: 8px;'>".__("플랜 목표","ajtodo")."</p>";
			$ret .= "		<p class='card-text' style=''>".nl2br($plan["plancomment"])."</p>";
			$ret .= "	</div>";
			$ret .= "</div>";
		}
		return $ret;
	}

	public function updateTodoOrder($planid, $orders){
		global $wpdb;
		return $wpdb->get_var("update ".AJTODO_DB_PLAN." set todoorderset = '".$orders."' where id =".$planid);
	}

	public function getStartPlan(){
		global $wpdb;
		$ingid = $wpdb->get_var("select id from ".AJTODO_DB_PLAN." where projectid =".$this->project->id." and ising = 'Y'");
		return $ingid;
	}

	public function startPlan($isstart){
		global $wpdb;
		$ret = false;
		if($isstart){
			$ing = $wpdb->get_var("select count(id) from ".AJTODO_DB_PLAN." where projectid =".$this->project->id." and ising = 'Y'");
			if($ing){
				$this->msg = __("진행중인 플랜이 존재합니다.","ajtodo");
				return $ret;
			}else{
				$ret = $wpdb->query("update ".AJTODO_DB_PLAN." set ising = 'Y' where id = ".$this->id);
				if($ret)
					do_action('ajtodo_start_plan', $this->project->id, $this->id);
				return $ret;
			}
		}else{
			$ret = $wpdb->query("update ".AJTODO_DB_PLAN." set ising = 'N' where id = ".$this->id);
			if($ret)
				do_action('ajtodo_stop_plan', $this->project->id, $this->id);

			return $ret;
		}
	}

	private function getTodoCountByPlan($key){
		global $wpdb;
		return $wpdb->get_var("select count(id) from ".AJTODO_DB_TODO."_".$this->project->id." where planid = ".$key);
	}

	public function getTodoCountByPlans(){
		global $wpdb;
		$sql = "select planid, IF(ISNULL(donedated), 'O', 'D') as statuskey, count(id) as cnt from ".AJTODO_DB_TODO."_".$this->project->id ;
		$sql .= " group by planid, statuskey";
		$list = $wpdb->get_results($sql, ARRAY_A);
		$this->project->plan[] = array("id" => "0", "plantitle" => __("플랜 없음","ajtodo") );
		foreach($this->project->plan as &$plan){
			$ismatsh = false;
			foreach($list as $l){
				if($plan["id"] == $l["planid"]){
					$ismatsh = true;
					$plan["cnt"][] = $l;
				}
			}
			if(!$ismatsh)
				$plan["cnt"] = array();
		}
	}

	public function delPlan($key){
		global $wpdb;
		$wpdb->get_var("delete from ".AJTODO_DB_PLAN." where id = ".$key);
		$wpdb->get_var("update ".AJTODO_DB_TODO."_".$this->project->id." set planid = NULL where planid = ".$key);
		do_action('ajtodo_del_plan', $this->project->id, $key);
		return true;
	}

	public static function getPlan($planid){
		global $wpdb;
		$sql = "select * from ".AJTODO_DB_PLAN." where id = ".$planid;
		$ret = $wpdb->get_row($sql);
		$ret = apply_filters('ajtodo_get_planinfo', $ret, $planid);
		return $ret;
	}

	public function finish($key){
		global $wpdb;
		$this->makeDoneTodoInPlan($key);
		$wpdb->query("update ".AJTODO_DB_PLAN." set donedate = '".date("Y-m-d H:i:s")."' where id = ".$key);
		do_action('ajtodo_finish_plan', $this->project->id, $key);
		return true;
	}

	public function makeDoneTodoInPlan($planid){
		global $wpdb;
		$todo = new AJTODO_Todo();
		$todo->project = $this->project;
		$colval = array(
			"statuskey" => "closed",
			"donedated" => ":_NOW_:"
		);
		
		$sql = "select * from ".AJTODO_DB_TODO."_".$this->project->id;
		$sql .= " where planid = ".$planid." and donedated is null;";
		foreach($wpdb->get_results($sql) as $item){
			$todo->id = $item->id;
			$todo->updateTodo("", "", json_encode($colval));
		}
	}

	public function getPlanProgress2($plankey){
		global $wpdb;
		$ret = array();
		$sql = "SELECT statuskey, count(*) as cnt FROM ".AJTODO_DB_TODO."_".$this->project->id." where planid = ".$plankey;
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

	public function getPlanProgress($plankey){
		global $wpdb;
		$ret = array();
		$sql = "SELECT statuskey, count(*) as cnt FROM ".AJTODO_DB_TODO."_".$this->project->id." where planid = ".$plankey;
		$sql .= " group by statuskey";
		$statuslist = $wpdb->get_results($sql, ARRAY_A);
		$closed = 0;
		$all = 0;
		foreach($statuslist as $status){
			if($status['statuskey'] == "closed"){
				$closed += $status['cnt'];
			}
			$all += $status['cnt'];
		}
		$perc = 0;
		if($all > 0){
			$perc = round(($closed * 100) / $all);
		}
		$ret = "<div class='progress'>";
		$ret .= "<div class='progress-bar' role='progressbar' ";
		$ret .= " style='width: $perc%;' aria-valuenow='$perc' aria-valuemin='0' ";
		$ret .= " aria-valuemax='100'>$perc%</div>";
		$ret .= "</div>";
		return $ret;
	}

	public function createFormPart($title, $isdone){
		$ret = "";
		$ret .= "<div class='fs18'>".$title;
		if(!$isdone){
			$ret .= " <a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=plan&act=addplan&pid=".$this->project->id."' class='btn btn-info btn-sm float-right'>".__('플랜 추가', 'ajtodo' )."</a>";
		}
		$ret .= "</div>";

		$ret .= "<table class='table ajtodo_members'>";
		$ret .= "<thead  class='thead-light'>";
		$ret .= "	<tr>";
		$ret .= "		<th scope='col' style='width:150px;'>".__("플랜 이름","ajtodo")."</th>";
		$ret .= "		<th scope='col' style='width:100px;'>".__("총할일수","ajtodo")."</th>";
		$ret .= "		<th scope='col' style='width:250px;'>".__("진행 상황","ajtodo")."</th>";
		$ret .= "		<th scope='col'>".__("시작일","ajtodo")."</th>";
		if($isdone){
			$ret .= "		<th scope='col'>".__("목표 완료일","ajtodo")."</th>";
			$ret .= "		<th scope='col'>".__("완료일","ajtodo")."</th>";
		}else{
			$ret .= "		<th scope='col'>".__("남은 일수","ajtodo")."</th>";
			$ret .= "		<th scope='col'>".__("목표 완료일","ajtodo")."</th>";
			$ret .= "		<th scope='col'>".__("완료일","ajtodo")."</th>";
		}
		$ret .= "		<th scope='col' style='width:250px;'>".__("관리","ajtodo")."</th>";
		$ret .= "	</tr>";
		$ret .= "</thead>";

		$ret .= "<tbody id='ajtodo_plan_table'>";
		foreach($this->project->plan as $plan){
			if($isdone){
				if(!$plan["donedate"])
					continue;
			}else{
				if($plan["donedate"])
					continue;
			}
			$ret .= "<tr class='".($isdone ? "plandone" : "")."'>";
			$ret .= "	<td scope='col'>".$plan["plantitle"]."</td>";
			$ret .= "	<td scope='col'>".$this->getTodoCountByPlan($plan["id"])."</td>";
			$ret .= "	<td scope='col'>".$this->getPlanProgress($plan["id"])."</td>";
			$ret .= "	<td scope='col'>".substr($plan["startdate"],0, 10)."</td>";
			if($plan["donedate"]){
				$ret .= "	<td scope='col'>".substr($plan["finishdate"],0, 10)."</td>";
				$ret .= "	<td scope='col'>".($plan["donedate"] ? substr($plan["donedate"],0, 10) : __("미완료","ajtodo"))."</td>";
			}else{
				$ret .= "	<td scope='col' class='ajtodo_remain' value='".substr($plan["finishdate"],0, 10)."'></td>";
				$ret .= "	<td scope='col'>".substr($plan["finishdate"],0, 10)."</td>";
				$ret .= "	<td scope='col'>".($plan["donedate"] ? substr($plan["donedate"],0, 10) : __("미완료","ajtodo"))."</td>";
			}
			$ret .= "	<td scope='col'>";
			$ret .= "		<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=plan&plankey=".$plan["id"]."&act=addplan&pid=".$this->project->id."' ";
			$ret .= " 		class='btn btn-success' id='ajtodo_edit_plan'>".__('관리', 'ajtodo' )."</a>";
			$ret .= "	</td>";
			$ret .= "<tr>";
		}
		$ret .= "</tbody>";
		$ret .= "</table>";
		return $ret;
	}

	public function getSinglePlan($plankey){
		foreach($this->project->plan as $s){
			if($s["id"] == $plankey){
				return $s;
			}
		}
	}

	public function addPlanView($plankey){
		if($plankey){
			$plan = $this->getSinglePlan($plankey);
		}else{
			$plan["donedate"] = "";
		}
		$url = "?page=ajtodo_admin_project&ptype=team&ajtodo_type=plan&pid=".$this->project->id;
		$ret = "";
		$ret .= "<form method='post' id='ajtodo_form'>";
		$ret .= "<input type='hidden' name='dotype' value='".($plankey ? "up" : "add")."'>";
		$ret .= "<input type='hidden' id='d_p_id' value='".$this->project->id."'>";
		$ret .= "<input type='hidden' id='ajtodo_plan_key' name='ajtodo_plan_key' value=".$plankey.">";
		$ret .= wp_nonce_field('ajtodo_nonce','update_roleperm');
		if(!$plan["donedate"]){
			$ret .= "<div class='form-group'>";
			$ret .= "	<label class='form-title'>".__('플랜 이름', 'ajtodo' )."</label>";
			$ret .= "	<div class='form-option'>";
			$ret .= "<input type='text' class='form-control form-control-sm' 
					value='".($plankey ? $plan["plantitle"] : "")."'
					name='ajtodo_plan_title' id='ajtodo_plan_title' style='width:250px'>";
			$ret .= "	</div>";
			$ret .= "</div>";

			$ret .= "<div class='form-group'>";
			$ret .= "	<label class='form-title'>".__('플랜 목표 설명', 'ajtodo' )."</label>";
			$ret .= "	<div class='form-option'>";
			$ret .= "	<textarea type='text' class='form-control form-control-sm'
						name='plancomment' id='plancomment' 
						style='width:500px;height:150px;'>".($plankey ? $plan["plancomment"] : "")."</textarea>";
			$ret .= "	</div>";
			$ret .= "</div>";

			$ret .= "<div class='form-group'>";
			$ret .= "	<label class='form-title'>".__('시작일', 'ajtodo' )."</label>";
			$ret .= "	<div class='form-option'>";
			$ret .= "<input type='date' class='input-control input-control-sm' 
					value=".($plankey ? $plan["startdate"] : date("Y-m-d"))."
					name='startdate' id='startdate'>";
			$ret .= "	</div>";
			$ret .= "</div>";

			$ret .= "<div class='form-group'>";
			$ret .= "	<label class='form-title'>".__('목표완료일', 'ajtodo' )."</label>";
			$ret .= "	<div class='form-option'>";
			$ret .= "<input type='date' class='input-control input-control-sm' 
					value=".($plankey ? $plan["finishdate"] : date("Y-m-d", strtotime("+14 day", time())))."
					name='finishdate' id='finishdate'>";
			$ret .= "	</div>";
			$ret .= "</div>";
		}else{
			$ret .= "<div class='form-group'>";
			$ret .= "	<label class='form-title'>".__('플랜 이름', 'ajtodo' )."</label>";
			$ret .= "	<div class='form-option'>";
			$ret .= "<span class='font-italic'>".$plan["plantitle"]."</span>";
			$ret .= "<input type='hidden' value='".$plan["plantitle"]."' name='ajtodo_plan_title' id='ajtodo_plan_title'>";
			$ret .= "	</div>";
			$ret .= "</div>";

			$ret .= "<div class='form-group'>";
			$ret .= "	<label class='form-title'>".__('플랜 목표 설명', 'ajtodo' )."</label>";
			$ret .= "	<div class='form-option'>";
			$ret .= "<div class='font-italic'>".nl2br($plan["plancomment"])."</div>";
			$ret .= "<input type='hidden' value='".$plan["plancomment"]."' name='plancomment' id='plancomment'>";
			$ret .= "	</div>";
			$ret .= "</div>";

			$ret .= "<div class='form-group'>";
			$ret .= "	<label class='form-title'>".__('시작일', 'ajtodo' )."</label>";
			$ret .= "	<div class='form-option'>";
			$ret .= "<span class='font-italic'>".$plan["startdate"]."</span>";
			$ret .= "<input type='hidden' value='".$plan["startdate"]."' name='startdate' id='startdate'>";
			$ret .= "	</div>";
			$ret .= "</div>";

			$ret .= "<div class='form-group'>";
			$ret .= "	<label class='form-title'>".__('완료일', 'ajtodo' )."</label>";
			$ret .= "	<div class='form-option'>";
			$ret .= "<span class='font-italic'>".$plan["donedate"]."</span>";
			$ret .= "<input type='hidden' value='".$plan["donedate"]."' name='finishdate' id='finishdate'>";
			$ret .= "	</div>";
			$ret .= "</div>";

			$ret .= "<div class='form-group'>";
			$ret .= "	<label class='form-title'>".__('목표완료일', 'ajtodo' )."</label>";
			$ret .= "	<div class='form-option'>";
			$ret .= "<span class='font-italic'>".$plan["finishdate"]."</span>";
			$ret .= "<input type='hidden' value='".$plan["finishdate"]."' name='finishdate' id='finishdate'>";
			$ret .= "	</div>";
			$ret .= "</div>";
		}

		if($plankey){
			if(!$plan["donedate"]){
				$ret .="<button id='btnSavePlan' class='btn btn-primary'>".__('플랜 수정','ajtodo')."</button>";
				$ret .="<a href='$url&act=finish&plankey=$plankey' class='btn btn-success'>".
					($plankey ? __('플랜 완료','ajtodo') : __('플랜 완료','ajtodo'))."</a>";
			}
			$ret .="<a href='$url&act=del&plankey=$plankey' class='btn btn-danger'>".
				($plankey ? __('플랜 삭제','ajtodo') : __('플랜 삭제','ajtodo'))."</a>";
		}else{
			$ret .="<button id='btnSavePlan' class='btn btn-primary'>".__('플랜 생성','ajtodo')."</button>";
		}
		$ret .="<a href='$url' class='btn btn-info'>".__('플랜 목록', 'ajtodo' )."</a>";
		$ret .= "</form>";
		return $ret;
	}

	public function addPlan($dotype, $plankey, $plantitle, $plancomment, $startdate, $finishdate){
		global $wpdb;
		$sql = "";
		$ret = false;
		if($dotype == "add"){
			$sql = "insert into ".AJTODO_DB_PLAN;
			$sql .= "(projectid, plantitle, plancomment, startdate, finishdate, regdate) values(";
			$sql .= $this->project->id.",";
			$sql .= "'".$plantitle."',";
			$sql .= "'".$plancomment."',";
			$sql .= "'".$startdate."',";
			$sql .= "'".$finishdate."',";
			$sql .= "'".date("Y-m-d H:i:s")."');";
			$ret = !($wpdb->query($sql) === FALSE);

			if($ret){
				$planid = $wpdb->insert_id;
				do_action('ajtodo_add_plan', $this->project->id, $planid);
			}
		}else{
			$sql = "update ".AJTODO_DB_PLAN." set ";
			$sql .= "plantitle = '".$plantitle."', ";
			$sql .= "plancomment = '".$plancomment."', ";
			$sql .= "startdate = '".$startdate."',";
			$sql .= "finishdate = '".$finishdate."' where id = ".$plankey;
			$ret = !($wpdb->query($sql) === FALSE);

			if($ret)
				do_action('ajtodo_modify_plan', $this->project->id, $plankey);
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

	public function finishConfirmView($plankey){
		$plan = $this->getSinglePlan($plankey);
		$ret = "";
		$ret .= "<form method='post' id='ajtodo_form'>";
		$ret .= "<input type='hidden' name='dotype' id='dotype'>";
		$ret .= wp_nonce_field('ajtodo_nonce','update_roleperm');
		$ret .= "<div id='ajtodo_form'>
			<div class='alert alert-success'>
				<div class='font-weight-bold fs18'>".__("플랜 이름","ajtodo")." : ".$plan["plantitle"]."</div>
				<div>".__("이 플랜을  완료제하시겠습니까?","ajtodo")."</div>
				<ul class='mt16 fs14'>
					<li>".__("총 할일 개수","ajtodo")." : <b>".$this->getTodoCountByPlan($plankey)."</b></li>
				</ul>
				<div>".__("플랜에 존재하는 미 완료 할일들은 모두 '완료 처리' 됩니다.","ajtodo")."</div>
			</div>
			<button class='btn btn-success' id='btnFinPlan'>".__("예, 완료합니다.","ajtodo")."</button>
			<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=plan&pid=".$this->project->id."' class='btn btn-primary'>".__("취소","ajtodo")."</a>
		</div>";
		$ret .= "</form>";
		return $ret;
	}

	public function delConfirmView($plankey){
		$plan = $this->getSinglePlan($plankey);
		$ret = "";
		$ret .= "<form method='post' id='ajtodo_form'>";
		$ret .= "<input type='hidden' name='dotype' id='dotype'>";
		$ret .= wp_nonce_field('ajtodo_nonce','update_roleperm');
		$ret .= "<div id='ajtodo_form'>
			<div class='alert alert-warning'>
				<div class='font-weight-bold fs18'>".__("플랜 이름","ajtodo")." : ".$plan["plantitle"]."</div>
				<div>".__("이 플랜을  삭제하시겠습니까?","ajtodo")."</div>
				<ul class='mt16 fs14'>
					<li>".__("총 할일 개수","ajtodo")." : <b>".$this->getTodoCountByPlan($plankey)."</b></li>
				</ul>
				<div>".__("플랜에 존재하는 할일들은 모두 '플랜  없음'이 됩니다.","ajtodo")."</div>
				<div>".__("플랜에 포함된 할일이 삭제되는 것은 아닙니다.","ajtodo")."</div>
			</div>
			<button class='btn btn-danger' id='btnDelPlan'>".__("예, 삭제합니다.","ajtodo")."</button>
			<a href='?page=ajtodo_admin_project&ptype=team&ajtodo_type=plan&pid=".$this->project->id."' class='btn btn-primary'>".__("취소","ajtodo")."</a>
		</div>";
		$ret .= "</form>";
		return $ret;
	}
}
