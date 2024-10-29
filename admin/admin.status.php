<?php
switch($ajtodo_view){
	case "list" :
        include(AJTODO_PLUGIN_PATH . "admin/admin.status.list.php");
		break;
	case "add" :
	case "edit" :
        include(AJTODO_PLUGIN_PATH . "admin/admin.status.add.php");
		break;
	case "del" :
        include(AJTODO_PLUGIN_PATH . "admin/admin.status.del.php");
		break;
}
?>
