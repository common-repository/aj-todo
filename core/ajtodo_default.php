<?php
class AJTODO_ProjectDefault{
	public static function DefaultProjectPerms() {
		return array(
			array("key" => "tp_dahsboard_view", "name" => __("대시보드 열람","ajtodo"), "built" => "Y", "desc" => __("대시보드를 볼 수 있는 권한입니다. ","ajtodo")),
			array("key" => "tp_category", "name" => __("카테고리 관리","ajtodo"), "built" => "Y", "desc" => __("할일 범주를 관리할 수 있는 권한입니다. ","ajtodo")),
			array("key" => "tp_status", "name" => __("상태 관리","ajtodo"), "built" => "Y", "desc" => __("상태를 관리할 수 있는 권한입니다. ","ajtodo")),
			array("key" => "tp_role_set", "name" => __("권한 설정","ajtodo"), "built" => "Y", "desc" => __("프로젝트 권한을 설정할 수 있는 권한입니다. ","ajtodo")),
			array("key" => "tp_role_member", "name" => __("사용자 역할 설정","ajtodo"), "built" => "Y", "desc" => __("사용자의 역할을 설정할 수 있는 권한입니다. ","ajtodo")),
			array("key" => "tp_invite", "name" => __("멤버 추가","ajtodo"), "built" => "Y", "desc" => __("멤버를 추가할 수  있는 권한입니다. ","ajtodo")),
			array("key" => "tp_member_manage", "name" => __("멤버 관리","ajtodo"), "built" => "Y", "desc" => __("프로젝트 멤버를 관리할 수 있는 권한입니다. ","ajtodo")),
			array("key" => "tp_project_close", "name" => __("프로젝트 종료","ajtodo"), "built" => "Y", "desc" => __("프로젝트를 종료할 수 있는 권한입니다. ","ajtodo")),
			array("key" => "tp_project_del", "name" => __("프로젝트 삭제","ajtodo"), "built" => "Y", "desc" => __("프로젝트를 삭제할 수 있는 권한입니다. ","ajtodo")),
		);
	}
	public static function DefaultPlanPerms() {
		return array(
			array("key" => "tp_plan_create", "name" => __("플랜 만들기","ajtodo"), "built" => "Y", "desc" => __("플랜을 만들고 관리할 수 있는 권한입니다. ","ajtodo")),
			array("key" => "tp_plan_manage", "name" => __("플랜 관리","ajtodo"), "built" => "Y", "desc" => __("플랜의 상태 변경/할일 설정을 할 수 있는 권한입니다. ","ajtodo")),
		);
	}
	public static function DefaultTodoPerms() {
		return array(
			array("key" => "tp_todo_create", "name" => __("할일 만들기","ajtodo"), "built" => "Y", "desc" => __("할일을 만들 수 있는 권한입니다. ","ajtodo")),
			array("key" => "tp_todo_del", "name" => __("할일 삭제","ajtodo"), "built" => "Y", "desc" => __("할일을 삭제 수 있는 권한입니다. ","ajtodo")),
			array("key" => "tp_todo_updateinfo", "name" => __("할일 수정","ajtodo"), "built" => "Y", "desc" => __("할일을 수정할 수 있는 권한입니다. ","ajtodo")),
			array("key" => "tp_todo_status", "name" => __("할일 상태 변경","ajtodo"), "built" => "Y", "desc" => __("할일의 상태를 변경할 수 있는 권한입니다. ","ajtodo")),
			array("key" => "tp_todo_project", "name" => __("할일 프로젝트 이동","ajtodo"), "built" => "Y", "desc" => __("할일의 프로젝트를 변경할 수 있는 권한입니다. ","ajtodo")),
			array("key" => "tp_todo_viewlist", "name" => __("할일 목록 보기","ajtodo"), "built" => "Y", "desc" => __("할일 목록을 볼 수 있는 권한입니다. ","ajtodo")),
		);
	}
	public static function DefaultDocPerms() {
		return array(
			array("key" => "tp_doc_create", "name" => __("문서 작성","ajtodo"), "built" => "Y", "desc" => __("문서를 작성/편집할 수 있는 권한입니다. ","ajtodo"))
		);
	}
	
	public static function DefaultTodoType() {
		$ret = array(
			array("key" => "task", "name" => __("작업","ajtodo"), "default" => "Y", "color" => "#1d3cb8"),
			array("key" => "bug", "name" => __("버그","ajtodo"), "default" => "N", "color" => "#bd0f0f"),
			array("key" => "improvement", "name" => __("개선","ajtodo"), "default" => "N", "color" => "#19912b"),
		);
		return json_encode($ret, JSON_UNESCAPED_UNICODE);
	}
	public static function DefaultRolePerms() {
		return '{"project_admin":["tp_dahsboard_view","tp_category","tp_status","tp_role_set","tp_role_member","tp_invite","tp_member_manage","tp_project_close","tp_project_del","tp_plan_create","tp_plan_manage","tp_todo_create","tp_todo_del","tp_todo_updateinfo","tp_todo_status","tp_todo_project","tp_todo_viewlist","tp_doc_create"],"project_manager":["tp_dahsboard_view","tp_category","tp_status","tp_role_set","tp_role_member","tp_invite","tp_member_manage","tp_project_close","tp_plan_create","tp_plan_manage","tp_todo_create","tp_todo_del","tp_todo_updateinfo","tp_todo_status","tp_todo_viewlist","tp_doc_create"],"project_user":["tp_dahsboard_view","tp_plan_create","tp_plan_manage","tp_todo_create","tp_todo_del","tp_todo_updateinfo","tp_todo_status","tp_todo_viewlist","tp_doc_create"]}';
	}
	public static function DefaultRoles($uid) {
		$ret = array(
			array("key" => "project_admin", "name" => __("관리자", "ajtodo"), "users" => array($uid)),
			array("key" => "project_manager", "name" => __("매니저", "ajtodo"), "users" => array($uid)),
			array("key" => "project_user", "name" => __("사용자", "ajtodo"), "users" => array($uid)),
		);
		return json_encode($ret, JSON_UNESCAPED_UNICODE);
	}
	public static function DefaultStatus() {
		$ret = array(
			array("key" => "open", 
				"name" => __("할일", "ajtodo"), 
				"rules" => array(
					array(
						"to" => "inprogress",
						"roles" => array("project_admin","project_manager","project_user")
					),
					array(
						"to" => "closed",
						"roles" => array("project_admin","project_manager","project_user")
					)
				),
				"statustype" => "S", 
				"icon" => "fa-square", 
				"color" => "primary" ),
			array("key" => "inprogress", 
				"name" => __("진행중", "ajtodo"), 
				"rules" => array(
					array(
						"to" => "open",
						"roles" => array("project_admin","project_manager","project_user")
					),
					array(
						"to" => "closed",
						"roles" => array("project_admin","project_manager","project_user")
					)
				),
				"statustype" => "I", 
				"icon" => "fa-caret-square-right", 
				"color" => "primary"),
			array("key" => "closed", 
				"name" => __("종료", "ajtodo"), 
				"rules" => array(
					array(
						"to" => "open",
						"roles" => array("project_admin","project_manager","project_user")
					)
				),
				"statustype" => "D", 
				"icon" => "fa-check-square", 
				"color" => "primary"),
		);
		return json_encode($ret, JSON_UNESCAPED_UNICODE);
	}
}
