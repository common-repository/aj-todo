<?php
function ajtodo_admin_setting(){
	$common = new AJTODO_Common();
	$common->loadCommon();
	$common->start("container");

    if( isset( $_GET["tab"] ) ) {
        $active_tab = sanitize_text_field( $_GET["tab"] );
    }else{
        $active_tab = "basic";
    }
    if( isset( $_GET["ajtodo_view"] ) ) {
        $ajtodo_view = sanitize_text_field( $_GET["ajtodo_view"] );
    }else{
        $ajtodo_view = "list";
    }
?>
<div class="ajtodo">
<?php
    if($active_tab == "basic"){
        require_once(AJTODO_PLUGIN_PATH . "admin/admin.basic.php");
    }else if($active_tab == "status"){
        require_once(AJTODO_PLUGIN_PATH . "admin/admin.status.php");
    }else if($active_tab == "role"){
        require_once(AJTODO_PLUGIN_PATH . "admin/admin.role.php");
    }
?>
</div>
<?php
	$common->last();
}
