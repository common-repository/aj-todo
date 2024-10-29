<?php
global $wp_roles;
$ajtodorole = new AJTODO_Role();
if(isset($_POST["ajtodo_editperm"])){
	$ajtodorole->updateRolesPerms($_POST);
}
$rolelist = $ajtodorole->getGlobalRoles();
?>
<style>
form.ajtodo_setting td, form.ajtodo_setting th {
	height:36px;
	text-align:center;
}
</style>
<h1>
	<?php echo __( 'AJ TODO 설정', 'ajtodo' )?>
</h1>
<?php include(AJTODO_PLUGIN_PATH . "admin/admin.tab.menu.php"); ?>
<div style="vertical-align:middle;padding:8px;">
	<div class="alert alert-info">
		<div><?php echo __("이 권한 설정은 Administrator만 접근이 가능합니다.","ajtodo")?></div>
		<div><?php echo __("프로젝트 권한은 프로젝트별로 설정됩니다.","ajtodo")?></div>
	</div>
	<form method="post" action="" class="ajtodo_setting">
		<input type="hidden" name="ajtodo_editperm" value="update">
		<h4><?php echo __("팀 프로젝트", "ajtodo")?></h4>
		<table class="table">
			<thead>
				<tr style='height:24px;'>
					<th><?php echo __("역할 이름", "ajtodo")?></th>
					<?php
					foreach($ajtodorole->init_globalroles as $r){
						echo "<th>".$r["name"]."</th>";
					}
					?>
				</tr>
			</thead>
			<tbody>
			<?php
				echo $ajtodorole->getFormRolesPerms();
			?>
			</tbody>
		</table>
		<input type="submit" class="button button-primary" value="<?php echo __('변경 사항 적용', 'ajtodo' )?>">
	</form>
</div>
<script>
</script>
