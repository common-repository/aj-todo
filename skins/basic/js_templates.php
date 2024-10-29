<?php
$js_templates = "
<script>
	var todo_lan = {
		adddoc : '".__("문서 작성","ajtodo")."',
		noplan : '".__("플랜 없음","ajtodo")."',
		ing : '".__("진행중","ajtodo")."',
		stop : '".__("중단","ajtodo")."',
		start : '".__("시작","ajtodo")."',
		finday : '".__("완료 목표일","ajtodo")."',
		regdate : '".__("작성일","ajtodo")."',
		donedate : '".__("완료일","ajtodo")."',
		nocategorykey : '".__("지정 안됨","ajtodo")."',
		notodotype : '".__("지정 안됨","ajtodo")."',
		nocontent : '".__("내용 없음","ajtodo")."',
		noassignee : '".__("담당자 없음","ajtodo")."',
		del : '".__("삭제","ajtodo")."',
		delconfirm : '".__("정말 삭제합니까?","ajtodo")."',
		projects : '".__("프로젝트","ajtodo")."',
		noproject : '".__("프로젝트 없음","ajtodo")."',
		loadingtext : '".__("로딩중...","ajtodo")."',
		todotype : '".__("할일 타입","ajtodo")."',
		showall : '".__("전체 보기","ajtodo")."',
		notdone : '".__("미완료","ajtodo")."',
		management : '".__("관리","ajtodo")."',
		show_ing_plan : '".__("완료된 플랜 제외","ajtodo")."',
		show_all_plan : '".__("전체 플랜 보기","ajtodo")."',
		done : '".__("완료","ajtodo")."',
		all : '".__("전체","ajtodo")."',
		ph_enter_role_name : '".__("역할 이름을 입력해주세요.","ajtodo")."'
	};
	var todo_option_html = `
	<div id='ajtodo_sv_inline_action'class='todo_option_inline clearfix float-right' val=''>
		<div class='todo_option_inline_box' class='float-right'>
			<div class='btn-group btn-group-sm' role='group' id='todo_status'>
			</div>
			<div class='btn-group btn-group-sm' role='group' id='todo_ac_docs'>
			</div>
			<div class='btn-group btn-group-sm float-right' role='group' id='todo_actions'>
				
			</div>
		</div>
	</div>`;
	var todo_view_html = `
	<div class='todo_singleview' val=''>
		<div class='clearfix'>
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
	</div>`;
</script>
";	
