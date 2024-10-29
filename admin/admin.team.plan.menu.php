<ul class="nav nav-navs">
	<!--
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "dashboard"){ echo "active";}?>" 
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=dashboard&pid=<?php echo $pid?>">
			<?php echo __("대시보드", "ajtodo")?>
		</a>
	</li>
	-->
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
	<?php if($prj->projecttype == "team"){?>
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "status"){ echo "active";}?>"
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=status&pid=<?php echo $pid?>">
			<?php echo __("상태 설정", "ajtodo")?>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "member"){ echo "active";}?>"
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=member&pid=<?php echo $pid?>">
			<?php echo __("프로젝트 멤버", "ajtodo")?>
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "role"){ echo "active";}?>"
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=role&pid=<?php echo $pid?>">
			<?php echo __("권한 설정", "ajtodo")?>
		</a>
	</li>
	<?php }?>
	<li class="nav-item">
		<a class="nav-link <?php if($ajtodo_type == "edit"){ echo "active";}?>"
			href="?page=ajtodo_admin_project&ptype=team&ajtodo_type=edit&pid=<?php echo $pid?>">
			<?php echo __("관리", "ajtodo")?>
		</a>
	</li>
</ul>
