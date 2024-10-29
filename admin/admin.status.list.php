<h1>
	<?php echo __( 'AJ TODO 설정', 'ajtodo' )?>
	<a href='?page=ajtodo_admin_setting&tab=status&ajtodo_view=add' class="button button-primary" ><?php echo __('할일 상태 만들기', 'ajtodo' )?></a>
</h1>
<?php include(AJTODO_PLUGIN_PATH . "admin/admin.tab.menu.php"); ?>
<div style="vertical-align:middle;padding:8px;" class='ajtodo_set_table'>
<?php
require_once(AJTODO_PLUGIN_PATH . "inc/ajtodo_status_table.php");
$ajtodo_status_table = new AJTODO_Status_Table();
$ajtodo_status_table->prepare_items();
$ajtodo_status_table->views();
$ajtodo_status_table->display();
?>
</div>
