<?php
get_header();
$todo = new AJTODO_Todo();
$todo->tkey = $todo_key;
$todo->setProject($todo_key);
?>
<article class="post" id="post-<?php echo $todo->tkey?>">
  <div class="post-body" style="padding:20px;border: 1px solid #e0e0e0; margin: 20px 0px;">
	<div class='ajtodo'>
	<div class='todo_singleview ajtodo_vp_view' style='font-size:14px;'>
		<div>
			<div>
				<span class='badge badge-primary fs12' id='ajtodo_sv_tkey' style='padding:4px 8px;'><?php echo $todo_key?></span>
				<span class='badge fs12 badge-info' id='ajtodo_sv_status' style='padding:4px 8px;'><?php echo $todo->status["name"]?></span>
			</div>
			<div class='font-weight-bold fs18' style='margin-top:4px;margin-bottom:0px' id='ajtodo_sv_title'><?php echo $todo->tData->title?></div>
			<div>
				<span class='fs12' style='margin-top:4px;margin-bottom:0px;margin-right:8px;' id='ajtodo_sv_regdate'><?php echo __("등록일시","ajtodo")." : ".$todo->tData->regdate?></span>
				<?php if($todo->tData->donedated){?>
				<span class='fs12' style='margin-top:4px;margin-bottom:0px' id='ajtodo_sv_donedate'><?php echo __("완료일시","ajtodo")." : ".$todo->tData->donedated?></span>
				<?php }?>
			</div>
		</div>
		<hr style='border-top: 1px solid #e0e0e0;'/>
		<table>
			<tr>
				<td style='padding:0px;padding-right:24px;font-size:14px;' class='isonlyteam'>
					<div class='ajtodo_todo_view_item'>
						<p style='margin-bottom:8px;'><i class='fas fa-hammer'></i> <?php echo __("담당자","ajtodo")?></p>
						<div class='font-weight-bold' id='ajtodo_sv_assignee' style='height: 24px; padding-top: 4px;'>
						<?php
							if($todo->tData->assignid){
								$ass = get_userdata($todo->tData->assignid);
								$ret = "<div class='inline_assignee'>";
								$ret .= str_replace("<img ", "<img class='rounded-circle' ", get_avatar($ass->ID, 24));
								$ret .= "<span style='margin-left:8px'>".$ass->display_name."</span>";
								$ret .= "</div>";
								echo $ret;
							}else{
								echo __("지정안됨","ajtodo");
							}
						?>
						</div>
					</div>
				</td>
				<td style='padding:0px;padding-right:24px;font-size:14px;' class='isonlyteam'>
					<div class='ajtodo_todo_view_item'>
						<p style='margin-bottom:8px;'><i class='fas fa-pen-alt'></i> <?php echo __("작성자","ajtodo")?></p>
						<div class='font-weight-bold' id='ajtodo_sv_author' style='height: 24px; padding-top: 4px;'>
						<?php
							if($todo->tData->authorid){
								$ass = get_userdata($todo->tData->authorid);
								$ret = "<div class='inline_assignee'>";
								$ret .= str_replace("<img ", "<img class='rounded-circle' ", get_avatar($ass->ID, 24));
								$ret .= "<span style='margin-left:8px'>".$ass->display_name."</span>";
								$ret .= "</div>";
								echo $ret;
							}else{
								echo __("지정안됨","ajtodo");
							}
						?>
						</div>
					</div>
				</td>
				<td style='padding:0px;padding-right:24px;font-size:14px;'>
					<div class='ajtodo_todo_view_item'>
						<p style='margin-bottom:8px;'><i class='fas fa-vote-yea'></i> <?php echo __("플랜","ajtodo")?></p>
						<div class='font-weight-bold' id='ajtodo_sv_plan' style='height: 24px; padding-top: 4px;'>
						<?php
							if($todo->tData->planid){
								echo $todo->plan->plantitle;
							}else{
								echo __("플랜 없음","ajtodo");
							}
						?>
						</div>
					</div>
				</td>
				<td style='padding:0px;padding-right:24px;font-size:14px;'>
					<div class='ajtodo_todo_view_item'>
						<p style='margin-bottom:8px;'><i class='fas fa-folder'></i> <?php echo __("카테고리","ajtodo")?></p>
						<div class='font-weight-bold' id='ajtodo_sv_category' style='height: 24px; padding-top: 4px;'>
						<?php
							if($todo->tData->categorykey){
								echo $todo->category["name"];
							}else{
								echo __("카테고리 없음","ajtodo");
							}
						?>
						</div>
					</div>
				</td>
				<td style='padding:0px;padding-right:24px;font-size:14px;'>
					<div class='ajtodo_todo_view_item'>
						<p style='margin-bottom:8px;'><i class='fas fa-vote-yea'></i> <?php echo __("이슈 타입","ajtodo")?></p>
						<div class='font-weight-bold' id='ajtodo_sv_todotype' style='height: 24px; padding-top: 4px;'>
						<?php
							if($todo->tData->todotype){
								echo $todo->todotype["name"];
							}else{
								echo __("할일 타입 없음","ajtodo");
							}
						?>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<hr style='border-top: 1px solid #e0e0e0;'/>
		<div id='ajtodo_content' class='ajtodo_todo_view_item'>
		<?php
			if($todo->tData->comment){
				echo nl2br($todo->tData->comment);
			}else{
				echo __("내용 없음","ajtodo");
			}
		?>
		</div>
	</div>
  </div>
  </div>
</article>
<?php
get_footer();
