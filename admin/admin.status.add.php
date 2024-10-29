<?php
global $wpdb;
if(isset($_POST["posttype"])) {
	$statusid = sanitize_text_field( $_POST["statusid"] );
	$posttype = sanitize_text_field( $_POST["posttype"] );
	$title = sanitize_text_field( $_POST["title"] );
	$comment = sanitize_textarea_field( $_POST["comment"] );

	if($posttype == "add"){
		$status = new AJTODO_Status();
		$status->title = $title;
		$status->comment = $comment;
		$status->statustype = "I";
		$status->statusorder = "A";
		$status->create();
		ajtodo_go( home_url()."/wp-admin/admin.php?page=ajtodo_admin_setting&tab=status" );
	}else if($posttype == "edit"){
		$status = new AJTODO_Status();
		$status->id = $statusid;
		$status->title = $title;
		$status->comment = $comment;
		$status->update();
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
	$status->id = $statusid;
	$status->setData();
}
?>
<h1>
	<?php echo __( 'AJ TODO 설정', 'ajtodo' )?>
</h1>
<?php include(AJTODO_PLUGIN_PATH . "admin/admin.tab.menu.php"); ?>
<div style="vertical-align:middle;padding:8px;" class='ajtodo_set_table'>
	<form method="post" action="" class="ajtodo_setting">
		<input type='hidden' name='statusid' value='<?php echo $statusid?>'>
		<input type='hidden' name='posttype' value='<?php echo $statusid ? "edit" : "add"?>'>
		<div class="form-group">
			<label class="form-title"><?php echo __('상태 이름', 'ajtodo' )?></label>
			<div class="form-option">
				<input type="text" id="ajtodo_title" value="<?php if($statusid){ echo $status->title; }?>" name="title">
			</div>
		</div>
		<div class="form-group">
			<label class="form-title"><?php echo __('상태 종류', 'ajtodo' )?></label>
			<div class="form-option">
				<p><?php echo __('진행중', 'ajtodo' )?></p>
				<p class="font-italic">
				<?php if($statusid){
					echo __('상태종류는 변경할 수 없습니다.', 'ajtodo' );
				}else{
					echo __('상태는 진행중 상태만 생성할 수 있습니다.', 'ajtodo' );
				}?>
				</p>
			</div>
		</div>
		<div class="form-group">
			<label class="form-title"><?php echo __('상태 설명', 'ajtodo' )?></label>
			<div class="form-option">
				<textarea name='comment' style='width: 400px;height: 60px;'><?php if($statusid){ echo $status->comment; }?></textarea>
			</div>
		</div>
		<?php if($statusid){?>
			<input type="submit" id='ajtodo_btn_edit' class="button button-primary" value="<?php echo __('변경 사항 적용', 'ajtodo' )?>">
			<?php }else{?>
			<input type="submit" class="button button-primary" value="<?php echo __('할일 상태 생성', 'ajtodo' )?>">
		<?php }?>
		<a href='?page=ajtodo_admin_setting&tab=status' class="button"><?php echo __('취소', 'ajtodo' )?></a>
	</form>
	<script>
	jQuery(document).ready(function($){
	});
	</script>
</div>
