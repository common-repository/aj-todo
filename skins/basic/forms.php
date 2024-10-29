<?php
$forms["CreateProject"] = "
<form class='form' style='max-width:600px;' id='ajtodo_form'>
	<div class='form-group'>
		<label for='ajtodo_title'>".__("프로젝트 이름","ajtodo")."</label>
		<input type='text' class='form-control form-control-sm'
			name='title'
			id='ajtodo_title' placeholder='".__("프로젝트 이름","ajtodo")."' value='[T_title]'>
	</div>
	<div class='form-group'>
		<label for='ajtodo_projecttype'>".__("프로젝트 타입","ajtodo")."</label>
		<div>
			<!--
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' 
					name='projecttype' 
					id='ajtodo_projecttype_team' 
					value='team' [R_projecttype_team]>
				<label class='form-check-label' for='ajtodo_projecttype_team'>
				".__("팀 프로젝트","ajtodo")."</label>
			</div>
			-->
			<div class='form-check form-check-inline'>
				<input class='form-check-input' type='radio' 
					name='projecttype' 
					id='ajtodo_projecttype_private' 
					value='private' [R_projecttype_private] [CreatePrivateProject]>
				<label class='form-check-label' for='ajtodo_projecttype_private'>
				".__("개인 프로젝트","ajtodo")."</label>
			</div>
		</div>
	</div>
	<div class='form-group'>
		<label for='ajtodo_comment'>".__("프로젝트 설명","ajtodo")."</label>
		<textarea type='text' class='form-control form-control-sm'
			name='comment'
			id='ajtodo_comment' style='width:100%;height:150px;'>[T_comment]</textarea>
	</div>
	<button class='btn btn-primary' id='btnCreateProject'>[L_ISCREATE]</button>
	<a href='?page=ajtodo_admin_project&ajtodo_type=del&id=[T_id]' class='btn btn-danger' id='btnRemoveProject' [L_REMOVE_BTNTSHOW]>".__("프로젝트 삭제하기","ajtodo")."</a>
	<a href='?page=ajtodo_admin_project&ajtodo_type=done&id=[T_id]' class='btn btn-success' [L_DONE_BTNTSHOW]>".__("프로젝트 완료하기","ajtodo")."</a>
	<a href='' val='[T_id]' id='btnActiveProject' class='btn btn-success' [L_ACTIVE_BTNTSHOW]>".__("프로젝트 활성화하기","ajtodo")."</a>
</form>
";
