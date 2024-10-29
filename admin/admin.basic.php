<?php
if($_POST){
    //update_option('ajtodo_start_user_filter', sanitize_text_field($_POST['ajtodo_start_user_filter'] ));
    //update_option('ajtodo_addproject_filter_auto', sanitize_text_field($_POST['ajtodo_addproject_filter_auto']));
    //update_option('ajtodo_auto_done_hidden', sanitize_text_field($_POST['ajtodo_auto_done_hidden'] ));
    update_option('ajtodo_del_noconfirm', sanitize_text_field($_POST['ajtodo_del_noconfirm'] ));
    update_option('ajtodo_noti_delay', sanitize_text_field($_POST['ajtodo_noti_delay'] ));
    update_option('ajtodo_set_autoclosedetailview', sanitize_text_field($_POST['ajtodo_set_autoclosedetailview'] ));
    update_option('ajtodo_set_showcatitem', sanitize_text_field($_POST['ajtodo_set_showcatitem'] ));
    update_option('ajtodo_set_showtodokey', sanitize_text_field($_POST['ajtodo_set_showtodokey'] ));
    update_option('ajtodo_set_defaultpage', sanitize_text_field($_POST['ajtodo_set_defaultpage'] ));
}

//$ajtodo_start_user_filter			= get_option('ajtodo_start_user_filter', "AR");
$ajtodo_addproject_filter_auto		= get_option('ajtodo_addproject_filter_auto', "Y");
$ajtodo_auto_done_hidden			= get_option('ajtodo_auto_done_hidden', "Y");
$ajtodo_del_noconfirm				= get_option('ajtodo_del_noconfirm', "Y");
$ajtodo_noti_delay					= get_option('ajtodo_noti_delay', "2000");
$ajtodo_set_autoclosedetailview		= get_option('ajtodo_set_autoclosedetailview', "Y");
$ajtodo_set_showcatitem				= get_option('ajtodo_set_showcatitem', "Y");
$ajtodo_set_showtodokey				= get_option('ajtodo_set_showtodokey', "Y");
$ajtodo_set_defaultpage				= get_option('ajtodo_set_defaultpage', "todo");
?>
	<h1>
		<?php echo __( 'AJ TODO 설정', 'ajtodo' )?>
	</h1>
	<?php //include(AJTODO_PLUGIN_PATH . "admin/admin.tab.menu.php"); ?>
    <div style="vertical-align:middle;padding:0px;">
		<form method="post" action="" class="ajtodo_setting">
			<!--
			<div class="form-group">
				<label class="form-title"><?php echo __('할일 기본 설정 ', 'ajtodo' )?></label>
				<div class="form-option">
					<input type="radio" id="ajtodo_s_s_f_1" value="OI" 
						name="ajtodo_start_status_filter" 
						<?php if($ajtodo_start_status_filter == "OI"){ echo "checked"; }?>>
						<label for="ajtodo_s_s_f_1"><?php echo __('미완료 할일들 보기', 'ajtodo' )?></label>
					<input type="radio" id="ajtodo_s_s_f_2" value="D" 
						name="ajtodo_start_status_filter" 
						<?php if($ajtodo_start_status_filter == "D"){ echo "checked"; }?>>
						<label for="ajtodo_s_s_f_2"><?php echo __('완료된 할일들 보기', 'ajtodo' )?></label>
					<input type="radio" id="ajtodo_s_s_f_3" value="" 
						name="ajtodo_start_status_filter" 
						<?php if($ajtodo_start_status_filter == ""){ echo "checked"; }?>>
						<label for="ajtodo_s_s_f_3"><?php echo __('전체 보기', 'ajtodo' )?></label>
				</div>
			</div>
			<div class="form-group">
				<label class="form-title"><?php echo __('할일 생성시 자동 프로젝트 지정', 'ajtodo' )?></label>
				<div class="form-option">
					<input type="radio" id="ajtodo_a_f_a_1" value="Y" 
						name="ajtodo_addproject_filter_auto" 
						<?php if($ajtodo_addproject_filter_auto == "Y"){ echo "checked"; }?>>
						<label for="ajtodo_a_f_a_1"><?php echo __('현재 보이는 프로젝트에 자동으로 포함됩니다.', 'ajtodo' )?></label>
					<input type="radio" id="ajtodo_a_f_a_2" value="N" 
						name="ajtodo_addproject_filter_auto" 
						<?php if($ajtodo_addproject_filter_auto == "N"){ echo "checked"; }?>>
						<label for="ajtodo_a_f_a_2"><?php echo __('프로젝트 없음으로 등록', 'ajtodo' )?></label>
				</div>
			</div>
			-->
			<div class="form-group">
				<label class="form-title"><?php echo __('프로젝트 메인 화면', 'ajtodo' )?></label>
				<div class="form-option">
					<input type="radio" id="ajtodo_s_d_p_1" value="todo" 
						name="ajtodo_set_defaultpage" 
						<?php if($ajtodo_set_defaultpage == "todo"){ echo "checked"; }?>>
						<label for="ajtodo_s_d_p_1"><?php echo __('할일 목록 화면', 'ajtodo' )?></label>
					<input type="radio" id="ajtodo_s_d_p_2" value="plan" 
						name="ajtodo_set_defaultpage" 
						<?php if($ajtodo_set_defaultpage == "plan"){ echo "checked"; }?>>
						<label for="ajtodo_s_d_p_2"><?php echo __('플랜 화면', 'ajtodo' )?></label>
				</div>
			</div>
			<div class="form-group">
				<label class="form-title"><?php echo __('목록에서 할일 키 보여주기', 'ajtodo' )?></label>
				<div class="form-option">
					<input type="radio" id="ajtodo_s_t_k_1" value="Y" 
						name="ajtodo_set_showtodokey" 
						<?php if($ajtodo_set_showtodokey == "Y"){ echo "checked"; }?>>
						<label for="ajtodo_s_t_k_1"><?php echo __('예', 'ajtodo' )?></label>
					<input type="radio" id="ajtodo_s_t_k_2" value="N" 
						name="ajtodo_set_showtodokey" 
						<?php if($ajtodo_set_showtodokey == "N"){ echo "checked"; }?>>
						<label for="ajtodo_s_t_k_2"><?php echo __('아니오', 'ajtodo' )?></label>
				</div>
			</div>
			<div class="form-group">
				<label class="form-title"><?php echo __('할일 뷰에서 할일 종료시 뷰 자동 닫기', 'ajtodo' )?></label>
				<div class="form-option">
					<input type="radio" id="ajtodo_s_a_d_1" value="Y" 
						name="ajtodo_set_autoclosedetailview" 
						<?php if($ajtodo_set_autoclosedetailview == "Y"){ echo "checked"; }?>>
						<label for="ajtodo_s_a_d_1"><?php echo __('예', 'ajtodo' )?></label>
					<input type="radio" id="ajtodo_s_a_d_2" value="N" 
						name="ajtodo_set_autoclosedetailview" 
						<?php if($ajtodo_set_autoclosedetailview == "N"){ echo "checked"; }?>>
						<label for="ajtodo_s_a_d_2"><?php echo __('그대로 남겨두기', 'ajtodo' )?></label>
				</div>
			</div>
			<div class="form-group">
				<label class="form-title"><?php echo __('할일에 카테고리 보여주기', 'ajtodo' )?></label>
				<div class="form-option">
					<input type="radio" id="ajtodo_s_s_c_1" value="Y" 
						name="ajtodo_set_showcatitem" 
						<?php if($ajtodo_set_showcatitem == "Y"){ echo "checked"; }?>>
						<label for="ajtodo_s_s_c_1"><?php echo __('예', 'ajtodo' )?></label>
					<input type="radio" id="ajtodo_s_s_c_2" value="N" 
						name="ajtodo_set_showcatitem" 
						<?php if($ajtodo_set_showcatitem == "N"){ echo "checked"; }?>>
						<label for="ajtodo_s_s_c_2"><?php echo __('아니오', 'ajtodo' )?></label>
				</div>
			</div>
			<div class="form-group">
				<label class="form-title"><?php echo __('할일 삭제시 한번더 확인하기', 'ajtodo' )?></label>
				<div class="form-option">
					<input type="radio" id="ajtodo_d_n_2" value="Y" 
						name="ajtodo_del_noconfirm" 
						<?php if($ajtodo_del_noconfirm == "Y"){ echo "checked"; }?>>
						<label for="ajtodo_d_n_2"><?php echo __('바로 삭제합니다.', 'ajtodo' )?></label>
					<input type="radio" id="ajtodo_d_n_1" value="N" 
						name="ajtodo_del_noconfirm" 
						<?php if($ajtodo_del_noconfirm == "N"){ echo "checked"; }?>>
						<label for="ajtodo_d_n_1"><?php echo __('삭제전에 한번더 물어봅니다.', 'ajtodo' )?></label>
				</div>
			</div>
			<div class="form-group">
				<label class="form-title"><?php echo __('알림 유지시간', 'ajtodo' )?></label>
				<div class="form-option">
					<select name="ajtodo_noti_delay">
						<option value="1000" <?php if($ajtodo_noti_delay == "1000") { echo "selected"; }?>>1초</option>
						<option value="2000" <?php if($ajtodo_noti_delay == "2000") { echo "selected"; }?>>2초</option>
						<option value="3000" <?php if($ajtodo_noti_delay == "3000") { echo "selected"; }?>>3초</option>
						<option value="5000" <?php if($ajtodo_noti_delay == "5000") { echo "selected"; }?>>5초</option>
						<option value="7000" <?php if($ajtodo_noti_delay == "7000") { echo "selected"; }?>>7초</option>
					</select>
				</div>
			</div>
			<input type="submit" class="button button-primary" value="<?php echo __('변경 사항 적용', 'ajtodo' )?>">
		</div>
        </form>
    </div>
<script>
jQuery(document).ready(function($){
	$("#ajtodo_d_d_2").click(function(e){
		$("#ajtodo_direct_done_status").show();
	});
	$("#ajtodo_d_d_1").click(function(e){
		$("#ajtodo_direct_done_status").hide();
	});
});
</script>
