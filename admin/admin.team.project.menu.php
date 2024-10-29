<ul class="nav nav-pills justify-content-end">
	<!--
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "dashboard"){ echo "active";}?>" 
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=dashboard&pid=<?php echo $pid?>">
			<?php echo __("대시보드", "ajtodo")?>
		</a>
	</li>
	-->
	<?php if($prj->projecttype == "team"){
		$nowplantitle = __("보드", "ajtodo");
		if(ajtodo_get("plankey", "")){
			foreach($prj->plan as $p){
				if($p["id"] == ajtodo_get("plankey", "")){
					$nowplantitle = __("보드","ajtodo")." ";
					if($p["donedate"] != ""){
						$nowplantitle .= "<i class='far fa-check-circle'></i> ";
					}else if($p["ising"] == "Y"){
						$nowplantitle .= "<i class='fas fa-play'></i> ";
					}else{
						$nowplantitle .= "<i class='far fa-circle'></i> ";
					}
					$nowplantitle .= $p["plantitle"];
					break;
				}
			}
		}
	?>
	<li class="nav-item dropdown">
		<a class="nav-link <?php if($ajtodo_type == "board"){ echo "active";}?> 
			dropdown-toggle" data-toggle="dropdown" href="#" role="button" 
			aria-haspopup="true" aria-expanded="false"><?php echo $nowplantitle?></a>
		<div class="dropdown-menu dropdown-menu-right" style='padding:0px;'>
			<?php foreach($prj->plan as $p){
				if($p["donedate"] || $p["id"] == ajtodo_get("planid", "")) { continue; }
			?>
			<a class="fs12 dropdown-item" 
				style='padding: 12px;' 
				href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=board&plankey=<?php echo $p["id"]?>&pid=<?php echo $prj->id?>">
			<?php
			if($p["donedate"] != ""){
				echo "<i class='far fa-check-circle'></i> ";
			}else if($p["ising"] == "Y"){
				echo "<i class='fas fa-play'></i> ";
			}else{
				echo "<i class='far fa-circle'></i> ";
			}?><?php echo $p["plantitle"]?>
				</a>
			<?php }?>
		</div>
	</li>
	<?php }?>
	<?php if( get_option('ajtodo_set_defaultpage', "todo") == "todo"){?>
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "todo"){ echo "active";}?>" 
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=todo&pid=<?php echo $pid?>">
			<?php echo __("할일", "ajtodo")?>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "plan"){ echo "active";}?>" 
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=plan&pid=<?php echo $pid?>">
			<?php echo __("플랜", "ajtodo")?>
		</a>
	</li>
	<?php }else{?>
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "plan"){ echo "active";}?>" 
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=plan&pid=<?php echo $pid?>">
			<?php echo __("플랜", "ajtodo")?>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "todo"){ echo "active";}?>" 
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=todo&pid=<?php echo $pid?>">
			<?php echo __("할일", "ajtodo")?>
		</a>
	</li>
	<?php }?>
	<?php if($prj->projecttype == "team"){?>
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "doc"){ echo "active";}?>"
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=doc&pid=<?php echo $pid?>">
			<?php echo __("문서", "ajtodo")?>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "report"){ echo "active";}?>"
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=report&pid=<?php echo $pid?>">
			<?php echo __("리포트", "ajtodo")?>
		</a>
	</li>
	<?php }?>
	<?php if($prj->projecttype == "private"){?>
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "category"){ echo "active";}?>"
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=category&pid=<?php echo $pid?>">
			<?php echo __("카테고리", "ajtodo")?>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "todotype"){ echo "active";}?>"
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=todotype&pid=<?php echo $pid?>">
			<?php echo __("할일 타입", "ajtodo")?>
		</a>
	</li>
	<?php }?>
	<?php if($prj->projecttype == "team"){?>
	<li class="nav-item dropdown">
		<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?php echo __("관리", "ajtodo")?></a>
		<div class="dropdown-menu dropdown-menu-right" style='padding: 0px;'>
			<a class="dropdown-item" href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=member&pid=<?php echo $pid?>"><?php echo __("멤버", "ajtodo")?></a>
			<a class="dropdown-item" href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=category&pid=<?php echo $pid?>"><?php echo __("카테고리", "ajtodo")?></a>
			<a class="dropdown-item" href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=todotype&pid=<?php echo $pid?>"><?php echo __("할일 타입", "ajtodo")?></a>
			<a class="dropdown-item" href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=role&pid=<?php echo $pid?>"><?php echo __("권한 설정", "ajtodo")?></a>
			<a class="dropdown-item" href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=status&pid=<?php echo $pid?>"><?php echo __("상태 설정", "ajtodo")?></a>
			<div class="dropdown-divider"></div>
			<a class="dropdown-item" href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=edit&pid=<?php echo $pid?>"><?php echo __("관리", "ajtodo")?></a>
		</div>
	</li>
	<?php }else{?>
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "role"){ echo "active";}?>"
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=role&pid=<?php echo $pid?>">
			<?php echo __("권한 설정", "ajtodo")?>
		</a>
	</li>
	<?php }?>
</ul>