<?php
function ajtodo_admin_project(){
	$ptype = isset($_GET["ptype"]) ? sanitize_text_field($_GET["ptype"]) : "private";
	switch($ptype){
		case "private":
			ajtodo_admin_project_private();
			break;
		case "team":
			ajtodo_admin_project_team();
			break;
	}
}