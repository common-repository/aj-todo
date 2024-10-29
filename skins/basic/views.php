<?php
$views["DivRowStart"] = "<div class='row'>";
$views["DivRowEnd"] = "</div>";
$views["ProjectCardContainerStart"] = "<div class='ajtodo_projectlist'>
<table class='table table-hover'>
	<thead>
		<tr>
			<th style='width:20%;'>".__("프로젝트명","ajtodo")."</th>
			<th style='width:20%;min-width:200px''>".__("진행상황","ajtodo")."</th>
			<th class='text-center'>".__("완료","ajtodo")."/".__("전체","ajtodo")."</th>
			<th>".__("설명","ajtodo")."</th>
			<th class='text-center'>".__("생성일","ajtodo")."</th>
			<th class='text-center'>".__("관리","ajtodo")."</th>
		</tr>
	</thead>
<tbody>";
$views["ProjectCard"] = "
	<tr>
		<td><a class='title' href='?page=ajtodo_admin_todo&pid=[T_id]'>[T_title]</a></td>
		<td style='padding:0px;'>
			<div class='progress'>
				<div class='progress-bar' role='progressbar' style='width: [T_percent]%;' aria-valuenow='[T_percent]' aria-valuemin='0' aria-valuemax='100'>[T_percent]%</div>
			</div>
		</td>
		<td class='text-center'>[T_doneissue]/[T_totalissue]</td>
		<td>[T_comment]</td>
		<td class='text-center'><span [V_id_HIDE]>[T_regdate]</span></td>
		<td class='text-center'><a [V_id_HIDE] href='?page=ajtodo_admin_project&ajtodo_type=edit&id=[T_id]' class='btn btn-info btn-sm'>".__("수정","ajtodo")."</a></td>
	</tr>";
$views["ProjectCardContainerEnd"] = "</tbody></table></div>";
$views["ProjectDoneCardContainerStart"] = "<div class='ajtodo_projectlist ajtodo_done'>
<table class='table table-hover'>
	<thead>
		<tr>
			<th style='width:20%;'>".__("프로젝트명","ajtodo")."</th>
			<th style='width:20%;min-width:200px''>".__("진행상황","ajtodo")."</th>
			<th class='text-center'>".__("완료","ajtodo")."/".__("전체","ajtodo")."</th>
			<th>".__("설명","ajtodo")."</th>
			<th class='text-center'>".__("생성일","ajtodo")."</th>
			<th class='text-center'>".__("관리","ajtodo")."</th>
		</tr>
	</thead>
<tbody>";
$views["ProjectDoneCardContainerEnd"] = "</tbody></table></div>";
$views["CreateTodo"] = "<div class='ajtodo_add_todo_inline'>
	<div class='input-group'>
		<input type='text' class='form-control form-control-sm' id='ajtodo_todotitle' placeholder='".__("할일을 입력해보세요.","ajtodo")."'>
		<!--
		<div class='input-group-append'>
			<button class='btn btn-outline-secondary dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>Dropdown</button>
			<div class='dropdown-menu'>
				<a class='dropdown-item' href='#'>Action</a>
				<a class='dropdown-item' href='#'>Another action</a>
				<a class='dropdown-item' href='#'>Something else here</a>
				<div role='separator' class='dropdown-divider'></div>
				<a class='dropdown-item' href='#'>Separated link</a>
			</div>
		</div>
		-->
	</div>
</div>";
$views["TodoListStart"] = "<ul class='list-group ajultodolist' id='todo_list'>";
$views["TodoListEnd"] = "</ul>";
$views["TodoListDoneStart"] = "<ul class='list-group ajultodolist' id='todo_done_list'>";
$views["TodoListDoneEnd"] = "</ul>";
$views["TodoListFilter"] = "<div class='col todo_filter_inline'>
	<div class='float-right'>
		<!--
		<div class='btn-group btn-group-sm' role='group' id='ajtodo_todolist_user_filter'>
			<button type='button' id='ajtodo_todolist_filter_assign' val='A' class='btn btn-secondary'>".__("내 할일","ajtodo")."</button>
			<button type='button' id='ajtodo_todolist_filter_author' val='R' class='btn btn-secondary'>".__("내가 보고한일","ajtodo")."</button>
			<button type='button' id='ajtodo_todolist_filter_all' val='AR' class='btn btn-secondary'>".__("전체","ajtodo")."</button>
		</div>
		-->
		<div class='btn-group btn-group-sm' role='group' id='ajtodo_todolist_status_filter'>
			<input type='text' class='form-control form-control-sm' id='sjtodo_search' placeholder='".__("검색","ajtodo")."'>
			<div class='btn-group'>
				<button type='button' class='btn dropdown-toggle' id='ajtodo_now_todotype'
					data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'></button>
				<div class='dropdown-menu' id='ajtodo_todotypelist' style='padding:0'>
				</div>
			</div>
			<div class='btn-group btn-group-sm' id='sjtodo_nowstatusinfo'>
			</div>
		</div>
	</div>
</div>";
$views["TodoActions"] = "<div class='todo_filter_inline clearfix'>
	<div class='float-right'>
		<div class='btn-group btn-group-sm' role='group' id='todo_actions'>
			<button type='button' id='del' val='O' class='btn btn-secondary'>".__("삭제","ajtodo")."</button>
		</div>
	</div>
</div>";
$views["UserProgress"] = "<div class='progress' style='height: 4px;' id='ajtodo_userprogress'>
  <div class='progress-bar bg-primary' role='progressbar' style='width: 0%;' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100'></div>
  <div class='progress-bar bg-warning' role='progressbar' style='width: 0%;' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100'></div>
  <div class='progress-bar bg-success' role='progressbar' style='width: 0%;' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100'></div>
</div>";
$views["TodoListProjectFilter"] = "<div class='col col-md-4 project_filter_inline' style='padding-left: 12px;'>
	<div class='btn-group btn-group-sm' id='todo_filter_project'>
	</div>
	<div class='btn-group btn-group-sm' id='todo_filter_plan' style='height:32px;'>
	</div>
</div>";
$views["Alert"] = "<div class=''>
	<div class='alert alert-danger' role='alert'>
		[ALERT_MSG]
	</div>
</div>";
$views["ProjectDelConfirm"] = "<div id='ajtodo_form'>
	<div class='alert alert-warning'>
		<div class='font-weight-bold fs18'>".__("프로젝트 이름","ajtodo")." : [T_title]</div>
		<div>".__("이 프로젝트를 삭제하시겠습니까?","ajtodo")."</div>
		<ul class='mt16 fs14'>
			<li>".__("총 할일 개수","ajtodo")." : <b>[T_totalissue]</b></li>
			<li>".__("완료된 할일  개수","ajtodo")." : <b>[T_doneissue]</b></li>
		</ul>
		<div>".__("프로젝트에 존재하는 할일들도 모두 같이 삭제됩니다.","ajtodo")."</div>
		<div>".__("삭제 이후에는 되돌릴 수 없습니다.","ajtodo")."</div>
	</div>
	<button class='btn btn-danger' id='btnDelProject'>".__("예, 삭제합니다.","ajtodo")."</button>
	<a href='?page=ajtodo_admin_project' class='btn btn-primary'>".__("취소","ajtodo")."</a>
</div>";
$views["ProjectDoneConfirm"] = "<div id='ajtodo_form'>
	<div class='alert alert-success'>
		<div class='font-weight-bold fs18'>".__("프로젝트 이름","ajtodo")." : [T_title]</div>
		<div>".__("이 프로젝트를 완료하시겠습니까?","ajtodo")."</div>
		<ul class='mt16 fs14'>
			<li>".__("총 할일 개수","ajtodo")." : <b>[T_totalissue]</b></li>
			<li>".__("완료된 할일  개수","ajtodo")." : <b>[T_doneissue]</b></li>
			<li>".__("완료율","ajtodo")." : <b>[T_percent]%</b></li>
		</ul>
		<div>".__("미완료된 할일들은 모두 완료처리 됩니다.","ajtodo")."</div>
		<div>".__("나중에도 언제든 다시 활성화할 수 있습니다.","ajtodo")."</div>
	</div>
	<button class='btn btn-success' id='btnDoneProject'>".__("예, 완료합니다.","ajtodo")."</button>
	<a href='?page=ajtodo_admin_project' class='btn btn-primary'>".__("취소","ajtodo")."</a>
</div>";
$views["Hello"] = "<div class='ajtodo_hello'>
	<div class='alert alert-info'>
		<div class='row'>
			<div class='col'>
				<div class='font-weight-bold fs16 mb8' id='ajtodo_hello_title'></div>
				<div id='ajtodo_hello_msg'></div>
			</div>
			<div class='col'>
				<button type='button' class='close' aria-label='Close'>
					<span aria-hidden='true' id='ajtodo_alert_close_big'>&times;</span>
				</button>
			</div>
		</div>
	</div>
</div>";
