<?php
global $wpdb;
if(isset($_POST["posttype"])) {
	$posttype = sanitize_text_field( $_POST["posttype"] );
	$statusid = sanitize_text_field( $_POST["statusid"] );
	$movestatusid = isset($_POST["movestatusid"]) ? sanitize_text_field( $_POST["movestatusid"] ) : "";
	if($posttype == "del"){
		$status = new AJTODO_Status();
		$status->id = $statusid;
		$status->del($movestatusid);
		ajtodo_go( home_url()."/wp-admin/admin.php?page=ajtodo_admin_setting&tab=status" );
	}
}
if( isset( $_GET["ajtodo_statusid"] ) ) {
	$statusid = sanitize_text_field( $_GET["ajtodo_statusid"] );
}else{
	$statusid = "";
}
if($statusid){
	$status = new AJTODO_Status();
	$statuslist = $status->getStatusList();
	$status->id = $statusid;
	$status->setData();
	$todoCount = $status->getTodoCount();
}
?>
<h1>
	<?php echo __( 'AJ TODO 설정', 'ajtodo' )?>
</h1>
<?php include(AJTODO_PLUGIN_PATH . "admin/admin.tab.menu.php"); ?>
<div style="vertical-align:middle;padding:8px;" class='ajtodo_set_table'>
	<form method="post" action="" class="ajtodo_setting">
		<input type='hidden' name='statusid' value='<?php echo $statusid?>'>
		<input type='hidden' name='posttype' value='del'>
		<div class="alert alert-danger" role="alert">
			<h4 class="alert-heading"><?php echo __("주의!", "ajtodo");?></h4>
			<p>
				<?php echo __("상태 삭제는 모든 사용자의 할일에 영향을 줍니다.", "ajtodo");?>
				<?php echo __("해당 상태인 할일이 존재하는 경우, 지정한 상태로 일괄 변경됩니다.", "ajtodo");?>
			</p>
			<hr>
			<p class="mb-0"><?php echo __("이 작업은 즉시 이루어집니다. 되돌릴 수 없습니다.", "ajtodo");?></p>
		</div>
		<div class="form-group">
			<label class="form-title"><?php echo __('삭제 하려는 상태', 'ajtodo' )?></label>
			<div class="form-option"><?php echo $status->title; ?></div>
		</div>
		<?php if($todoCount){?>
		<div class="form-group">
			<label class="form-title">"<?php echo $status->title; ?>" 상태 할일 개수</label>
			<div class="form-option">
				<p class="font-weight-bold"><?php echo $todoCount?></p>
			</div>
		</div>
		<div class="form-group">
			<label class="form-title"><?php echo __('선택된 상태로 모두 변경', 'ajtodo' )?></label>
			<div class="form-option">
				<select name="movestatusid">
				<?php
					foreach($statuslist as $s){
						if($s->id != $statusid){
							echo "<option value=".$s->id.">".$s->title."</option>";
						}
					}
				?>
				</select>
			</div>
		</div>
		<?php }?>
		<input type="submit" id='ajtodo_btn_del' class="button button-primary" value="<?php echo __('상태 삭제', 'ajtodo' )?>">
		<a href='?page=ajtodo_admin_setting&tab=status' class="button"><?php echo __('취소', 'ajtodo' )?></a>
	</form>
	<script>
	jQuery(document).ready(function($){
	});
	</script>
</div>
