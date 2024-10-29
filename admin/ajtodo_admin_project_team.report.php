<?php
function ajtodo_admin_project_team_report($prj){
	$aj_planid = ajtodo_get("planid", "");
	wp_enqueue_script('ajtodo_muurijs', AJTODO_JS_PATH."muuri.min.js", array('jquery'), '1.0', true );
	wp_enqueue_script('ajtodo_reportjs', AJTODO_JS_PATH."ajtodo_report.".AJTODO_JSMIN."js", array('jquery'), '1.0', true );
	wp_enqueue_script('ajtodo_chartjs', AJTODO_JS_PATH."Chart.min.js", array('jquery'), '1.0', true );
	wp_register_style('ajtodo_chartcss', AJTODO_PLUGIN_URL.'css/Chart.min.css');
	wp_enqueue_style('ajtodo_chartcss');

	wp_localize_script('ajtodo_reportjs', 'ajax_report', array(
		'dailyremains' => json_encode($prj->getDailyRemainIssues($aj_planid)),
		'planid' => $aj_planid
	));
	
	echo "<div class='btn-toolbar' role='toolbar' aria-label='Toolbar with button groups'>";
	echo $prj->getPlanSelector($aj_planid);
	echo $prj->quickLink($aj_planid);
	echo "</div>";
	echo "<div class='aj_report'>";
	echo "	<div class='aj_row'>";
	echo "		<div class='aj_col aj_report_mason'>";
	echo $prj->getReportSummary($aj_planid);
	if($aj_planid){
		echo $prj->getLinkedDocs($aj_planid);
	}
	echo "		</div>";
	echo "		<div class='aj_col aj_report_mason'>";
	if($aj_planid){
		echo $prj->getDailRemainChart($aj_planid);
	}else{
		echo $prj->getLinkedDocs($aj_planid);
	}
	echo "		</div>";
	echo "		<div class='aj_col aj_report_mason'>";
	echo $prj->getReportIssueByTodoType($aj_planid);
	echo $prj->getReportIssueByUsers($aj_planid);
	echo $prj->getReportIssueByCategory($aj_planid);
	echo "		</div>";
	echo "	</div>";
	echo "</div>";
}
